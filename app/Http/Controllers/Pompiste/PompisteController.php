<?php

namespace App\Http\Controllers\Pompiste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Personnel;
use App\Models\Soute;
use App\Models\Distributeur;
use App\Models\Carburant;
use App\Models\Depotage;
use App\Models\Distribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StatistiquesExport;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class PompisteController extends Controller
{
    public function distribution(Request $request)
    {
        $personnel = Auth::guard('personnel_soute')->user();
        if (!$personnel) {
            return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Veuillez vous connecter.']);
        }

        $activeSouteId = session('active_soute_id');

        if (!$activeSouteId) {
            return redirect()->route('soute.dashboard.index') // Redirige vers le dashboard principal de la soute
                             ->withErrors(['error' => 'Aucune soute active n\'a été sélectionnée. Veuillez retourner au tableau de bord.']);
        }

        // Récupérer la soute et s'assurer que le personnel y est lié
        $soute = $personnel->soutes()->find($activeSouteId);

        if (!$soute) {
            // Si la soute n'est pas trouvée ou si le personnel n'y est pas lié via la relation soutes()
            // Déconnecter et rediriger peut être une mesure de sécurité
            Auth::guard('personnel_soute')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            session()->forget('active_soute_id');
            return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Soute non trouvée ou accès non autorisé.']);
        }
  // dd($soute->types_carburants_stockes, $soute->niveau_actuel_essence, $soute->niveau_actuel_kerozen, $soute->niveau_actuel_diesel);
        // La vue 'pompiste.services.distribution' recevra l'objet $soute et $personnel
        return view('pompiste.services.distribution', compact('personnel', 'soute'));
    }
    public function storeDistribution(Request $request)
    {
        $rules = [
            'soute_id' => 'required|exists:soutes,id',
            'nom_chauffeur' => 'required|string|max:255',
            'immatriculation_vehicule' => 'required|string|max:20',
            'produit' => 'required|in:essence,kerozen,diesel', // Clés en minuscules
            'quantite' => 'required|numeric|min:0.01',
            'date_depotage' => 'required|date_format:Y-m-d',
            'heure_depotage' => 'required|date_format:H:i',
        ];

        $messages = [
            'produit.in' => 'Le type de carburant sélectionné est invalide.',
            'soute_id.exists' => 'La soute spécifiée est invalide.',
            // Ajoutez d'autres messages personnalisés si nécessaire
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator, 'distribution_modal') // Utiliser le named error bag
                        ->withInput();
        }

        $validated = $validator->validated(); // Récupérer les données validées

        $pompiste = Auth::guard('personnel_soute')->user();
        if (!$pompiste) {
            // Rediriger avec un message d'erreur spécifique pour le modal
            return redirect()->back()
                        ->with('error_modal', 'Session invalide. Veuillez vous reconnecter.')
                        ->withInput();
        }

        $soute = Soute::findOrFail($validated['soute_id']);

        if (!$pompiste->soutes()->where('soutes.id', $soute->id)->exists()) {
            return redirect()->back()
                        ->with('error_modal', 'Accès non autorisé à cette soute.')
                        ->withInput();
        }

        $quantiteDistribuee = (float)$validated['quantite'];
        $typeCarburantDemande = $validated['produit']; // 'essence', 'kerozen', ou 'diesel'

        // Noms des champs basés sur le type de carburant demandé
        $champCapacite = 'capacite_' . $typeCarburantDemande;         // ex: capacite_essence
        $champNiveauActuel = 'niveau_actuel_' . $typeCarburantDemande; // ex: niveau_actuel_essence

        // Déterminer le stock actuel disponible pour ce carburant
        $stockCourantPourDistribution = 0;
        // Vérifier si le champ niveau_actuel pour ce carburant existe et n'est pas null
        $estPremiereDistributionPourCeCarburant = ($soute->{$champNiveauActuel} === null);

        if ($estPremiereDistributionPourCeCarburant) {
            // Si niveau_actuel_... est NULL, le stock disponible est la capacité de ce carburant
            // Assurez-vous que la soute a bien une colonne pour cette capacité.
            if (!isset($soute->{$champCapacite})) {
                 return back()->withErrors(
                    ['erreur_serveur' => 'Configuration de la capacité manquante pour ' . ucfirst($typeCarburantDemande) . '.'],
                    'distribution_modal'
                )->withInput();
            }
            $stockCourantPourDistribution = (float)$soute->{$champCapacite};
        } else {
            // Sinon, c'est le niveau_actuel_... enregistré
            $stockCourantPourDistribution = (float)$soute->{$champNiveauActuel};
        }

        // Vérifier la quantité disponible
        if ($stockCourantPourDistribution < $quantiteDistribuee) {
            return back()->withErrors(
                ['quantite' => 'Quantité de ' . ucfirst($typeCarburantDemande) . ' insuffisante. Stock disponible: ' . number_format($stockCourantPourDistribution, 2) . 'L.'],
                'distribution_modal'
            )->withInput();
        }

        DB::beginTransaction();
        try {
            Distribution::create([
                'personnel_id' => $pompiste->id,
                'soute_id' => $soute->id,
                'nom_chauffeur' => $validated['nom_chauffeur'],
                'immatriculation_vehicule' => $validated['immatriculation_vehicule'],
                'type_carburant' => $typeCarburantDemande,
                'quantite' => $quantiteDistribuee,
                'date_depotage' => $validated['date_depotage'],
                'heure_depotage' => $validated['heure_depotage'],
            ]);

            // Mettre à jour la soute : on met TOUJOURS à jour le champ niveau_actuel_...
            $nouveauNiveau = $stockCourantPourDistribution - $quantiteDistribuee;
            $soute->{$champNiveauActuel} = $nouveauNiveau;
            $soute->save();

            DB::commit();

            // Important: S'assurer que la clé de session pour le message de succès correspond à celle lue dans le modal
            return redirect()->route('soute.dashboard.services.distribution')
                             ->with('success_modal', 'Distribution enregistrée! Nouveau stock ' . ucfirst($typeCarburantDemande) . ': ' . number_format($nouveauNiveau, 2) . 'L.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur store distribution: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Utiliser error_modal pour les erreurs générales serveur
            return back()->with('error_modal', 'Une erreur serveur est survenue: ' . $e->getMessage())->withInput();
        }
    }
    public function depotage(Request $request)
    {
        // Logique pour la page de dépotage, similaire pour récupérer la soute si nécessaire
        $personnel = Auth::guard('personnel_soute')->user();
        $activeSouteId = session('active_soute_id');
        if (!$activeSouteId) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Aucune soute active.']);
        }
        $soute = $personnel->soutes()->find($activeSouteId);
        if (!$soute) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Soute non trouvée ou accès non autorisé.']);
        }
        return view('pompiste.services.depotage', compact('personnel', 'soute'));
    }
    public function storeDepotage(Request $request)
    {
        $rules = [
            'soute_id' => 'required|exists:soutes,id',
            'date_depotage' => 'required|date_format:Y-m-d',
            'heure_depotage' => 'required|date_format:H:i',
            'nom_operateur' => 'required|string|max:255',
            'nom_societe_transporteur' => 'required|string|max:255',
            'nom_chauffeur_transporteur' => 'required|string|max:255',
            'immatriculation_vehicule_transporteur' => 'required|string|max:255',
            'produit' => 'required|in:essence,kerozen,diesel', // Clés en minuscules
            'volume_transporte_l' => 'required|numeric|min:0',
            'numero_bon_livraison' => 'nullable|string|max:255',
            'niveau_avant_depotage_l' => 'required|numeric', // Validé comme venant du JS
            'volume_recu_l' => 'required|numeric|min:0.01', // Doit recevoir au moins un peu
            'observations' => 'nullable|string',
        ];

        $messages = [
            'produit.in' => 'Le type de produit sélectionné pour le dépotage est invalide.',
            'soute_id.exists' => 'La soute spécifiée pour le dépotage est invalide.',
            'volume_recu_l.min' => 'Le volume reçu doit être supérieur à zéro.'
            // ... autres messages personnalisés
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator, 'depotage_modal')
                        ->withInput();
        }

        $validated = $validator->validated();
        $pompiste = Auth::guard('personnel_soute')->user();

        if (!$pompiste) {
            return redirect()->back()
                        ->with('error_depotage_modal', 'Session invalide. Veuillez vous reconnecter.')
                        ->withInput();
        }

        $soute = Soute::findOrFail($validated['soute_id']);

        if (!$pompiste->soutes()->where('soutes.id', $soute->id)->exists()) {
            return redirect()->back()
                        ->with('error_depotage_modal', 'Accès non autorisé à cette soute pour le dépotage.')
                        ->withInput();
        }

        $typeCarburantDepote = $validated['produit']; // 'essence', 'kerozen', 'diesel'
        $volumeReellementRecu = (float)$validated['volume_recu_l'];

        $champCapacite = 'capacite_' . $typeCarburantDepote;
        $champNiveauActuel = 'niveau_actuel_' . $typeCarburantDepote;

        DB::beginTransaction();
        try {
            // 1. Enregistrer l'opération de dépotage
            Depotage::create([
                'soute_id' => $soute->id,
                'personnel_id' => $pompiste->id,
                'date_depotage' => $validated['date_depotage'],
                'heure_depotage' => $validated['heure_depotage'],
                'nom_operateur' => $validated['nom_operateur'],
                'nom_societe_transporteur' => $validated['nom_societe_transporteur'],
                'nom_chauffeur_transporteur' => $validated['nom_chauffeur_transporteur'],
                'immatriculation_vehicule_transporteur' => $validated['immatriculation_vehicule_transporteur'],
                'produit' => $typeCarburantDepote,
                'volume_transporte_l' => $validated['volume_transporte_l'],
                'numero_bon_livraison' => $validated['numero_bon_livraison'],
                'niveau_avant_depotage_l' => $validated['niveau_avant_depotage_l'], // C'est le niveau AVANT ce dépotage
                'volume_recu_l' => $volumeReellementRecu,
                'observations' => $validated['observations'],
            ]);

            // 2. Mettre à jour le stock de la soute
            // On ajoute le volume reçu au niveau actuel.
            // Si niveau_actuel était NULL (première opération sur ce carburant), on part de 0 ou de la capacité si c'est la logique.
            // Mais ici, pour un dépotage, on ajoute au stock existant.

            $niveauAvantCeDepotage = 0;
            if ($soute->{$champNiveauActuel} !== null) {
                $niveauAvantCeDepotage = (float)$soute->{$champNiveauActuel};
            } else {
                // Si c'est la première fois qu'on met à jour niveau_actuel_... pour ce produit,
                // et qu'il était NULL, cela signifie que le "stock avant distribution" était la capacité.
                // Mais pour un dépotage, si niveau_actuel est NULL, on considère que le niveau était 0 avant ce remplissage.
                // Ou, si la logique est que `niveau_actuel` n'est mis à jour qu'après une *distribution*,
                // alors on pourrait prendre `capacite_` comme point de départ si `niveau_actuel` est NULL.
                // Pour simplifier et être cohérent : si `niveau_actuel_XXX` est NULL, on le considère comme 0 avant d'ajouter le volume reçu.
                 $niveauAvantCeDepotage = 0; // Ou (float)$soute->{$champCapacite} si vous voulez que le dépotage "remplisse" par rapport à la capacité totale quand niveau_actuel est null.
                                            // Cependant, ajouter à 0 est plus direct pour un dépotage.
            }

            $nouveauNiveauCalcule = $niveauAvantCeDepotage + $volumeReellementRecu;
            $capaciteMaxSoutePourCeProduit = (float)$soute->{$champCapacite};

            // Gérer le dépassement de capacité
            if ($nouveauNiveauCalcule > $capaciteMaxSoutePourCeProduit) {
                // Option 1: Limiter au maximum de la capacité et logger/notifier le surplus
                // $soute->{$champNiveauActuel} = $capaciteMaxSoutePourCeProduit;
                // \Log::warning("Dépotage pour soute {$soute->id}, produit {$typeCarburantDepote}: Dépassement de capacité. Reçu: {$volumeReellementRecu}, Niveau avant: {$niveauAvantCeDepotage}, Capacité: {$capaciteMaxSoutePourCeProduit}. Niveau mis à capacité max.");

                // Option 2: Permettre le dépassement et le stocker (peut nécessiter d'ajuster la vue du dashboard)
                 $soute->{$champNiveauActuel} = $nouveauNiveauCalcule;
                 \Log::info("Dépotage pour soute {$soute->id}, produit {$typeCarburantDepote}: Dépassement de capacité enregistré. Nouveau niveau: {$nouveauNiveauCalcule} L (Capacité: {$capaciteMaxSoutePourCeProduit} L).");


                // Vous devez choisir une stratégie ici. Pour l'instant, je vais permettre le dépassement.
            } else {
                 $soute->{$champNiveauActuel} = $nouveauNiveauCalcule;
            }

            $soute->save();

            DB::commit();

            return redirect()->route('soute.dashboard.services.depotage') // Redirige vers la page de dépotage
                             ->with('success_depotage_modal', 'Dépotage enregistré avec succès! Nouveau niveau de ' . ucfirst($typeCarburantDepote) . ': ' . number_format($soute->{$champNiveauActuel}, 2) . 'L.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur store dépotage: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error_depotage_modal', 'Une erreur serveur est survenue lors de l\'enregistrement du dépotage: ' . $e->getMessage())->withInput();
        }
    }
 

    public function rapport(Request $request)
    {
        $personnel = Auth::guard('personnel_soute')->user();
        if (!$personnel) {
            return redirect()->route('soute.dashboard.login')->withErrors(['Veuillez vous connecter.']);
        }
        
        // Données pour le tableau (avec filtres)
        $statistiquesFiltrees = $this->getStatistiquesData($request);
        
        // Données pour le graphique (sans filtres)
        $statistiquesGraphique = $this->getStatistiquesData(new Request(), true);
    
        // Préparer les données pour le graphique
        $labelsGraphique = [];
        $donneesDistributionGraphique = [];
        $donneesDepotageGraphique = [];
    
        foreach ($statistiquesGraphique as $statMois) {
            $labelsGraphique[] = $statMois->mois;
            $donneesDistributionGraphique[] = $statMois->distribution;
            $donneesDepotageGraphique[] = $statMois->depotage;
        }
    
        $pompisteStats = [
            'pompiste' => ['nom' => $personnel->nom_complet ?? 'Personnel Connecté', 'id' => $personnel->id],
            'date_debut_filtre' => $request->input('date_debut'),
            'date_fin_filtre' => $request->input('date_fin'),
        ];
    
        $viewData = [
            'personnel' => $personnel,
            'pompisteStats' => $pompisteStats,
            'labels' => $labelsGraphique,
            'data' => $donneesDistributionGraphique,
            'dataDepotage' => $donneesDepotageGraphique,
            'statistiques' => $statistiquesFiltrees,
            'selectedDateDebut' => $request->input('date_debut'),
            'selectedDateFin' => $request->input('date_fin'),
        ];
        return view('pompiste.rapport.index', $viewData);
    }

 // Dans PompisteController.php

public function exportPdf(Request $request) // Ajout de Request
{
    // Pour un export simple, on pourrait ne pas appliquer les filtres de date de la requête
    // ou alors il faudrait une logique pour stocker les filtres en session ou les passer en GET
    // Ici, on va créer une nouvelle requête "vide" pour getStatistiquesData pour obtenir les données de l'année courante.
    $statistiques = $this->getStatistiquesData(new Request()); // Passe une requête vide pour données non filtrées par date
    $data = [
        'statistiques' => $statistiques,
        'titre' => 'Rapport des Statistiques Mensuelles',
        'dateExport' => now()->format('d/m/Y H:i')
    ];
    $pdf = Pdf::loadView('pompiste.rapport.statistiques-pdf', $data);
    return $pdf->download('statistiques-mensuelles.pdf');
}

public function exportExcel(Request $request) // Ajout de Request
{
    $statistiques = $this->getStatistiquesData(new Request()); // Passe une requête vide
    return Excel::download(new StatistiquesExport($statistiques), 'statistiques-mensuelles.xlsx');
}
private function getStatistiquesData(Request $request, $ignoreFilters = false)
{
    $personnel = Auth::guard('personnel_soute')->user();
    if (!$personnel) {
        return collect();
    }

    // Déterminer l'année de référence
    $anneeDeReference = Carbon::now()->year;
    if (!$ignoreFilters && $request->input('date_debut')) {
        $anneeDeReference = Carbon::parse($request->input('date_debut'))->year;
    }

    $resultats = collect();

    for ($m = 1; $m <= 12; $m++) {
        $moisCarbon = Carbon::createFromDate($anneeDeReference, $m, 1);
        $nomMois = $moisCarbon->translatedFormat('F');

        // Requêtes de base
        $queryDistributions = Distribution::where('personnel_id', $personnel->id)
                                        ->whereYear('date_depotage', $anneeDeReference)
                                        ->whereMonth('date_depotage', $m);

        $queryDepotages = Depotage::where('personnel_id', $personnel->id)
                                ->whereYear('date_depotage', $anneeDeReference)
                                ->whereMonth('date_depotage', $m);

        // Appliquer les filtres seulement si on ne les ignore pas
        if (!$ignoreFilters && $request->input('date_debut') && $request->input('date_fin')) {
            $parsedDateDebut = Carbon::parse($request->input('date_debut'))->startOfDay();
            $parsedDateFin = Carbon::parse($request->input('date_fin'))->endOfDay();

            if ($moisCarbon->betweenIncluded($parsedDateDebut->copy()->startOfMonth(), $parsedDateFin->copy()->endOfMonth())) {
                $queryDistributions->whereBetween('date_depotage', [$parsedDateDebut, $parsedDateFin]);
                $queryDepotages->whereBetween('date_depotage', [$parsedDateDebut, $parsedDateFin]);
            } else {
                $queryDistributions->whereRaw('1 = 0');
                $queryDepotages->whereRaw('1 = 0');
            }
        }

        $totalDistributionMois = $queryDistributions->sum('quantite');
        $totalDepotageMois = $queryDepotages->sum('volume_recu_l');

        $resultats->push((object) [
            'mois' => $nomMois,
            'distribution' => $totalDistributionMois ?? 0,
            'depotage' => $totalDepotageMois ?? 0,
        ]);
    }
    return $resultats;
}

}
