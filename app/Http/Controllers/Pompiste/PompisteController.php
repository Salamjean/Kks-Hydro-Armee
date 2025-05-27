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

        $productsDataStatic = [
            ['name' => 'Janvier', 'sales' => 1540, 'depotage' => 1200],
            ['name' => 'Fevrier', 'sales' => 2240, 'depotage' => 2100],
            ['name' => 'Mars', 'sales' => 1840, 'depotage' => 1600],
            ['name' => 'Avril', 'sales' => 2040, 'depotage' => 1800],
            ['name' => 'Mai', 'sales' => 1740, 'depotage' => 1500],
            ['name' => 'Juin', 'sales' => 1940, 'depotage' => 1700],
            ['name' => 'Juillet', 'sales' => 2140, 'depotage' => 1900],
            ['name' => 'Aout', 'sales' => 2340, 'depotage' => 2100],
            ['name' => 'Septembre', 'sales' => 2540, 'depotage' => 2300],
            ['name' => 'Octobre', 'sales' => 2740, 'depotage' => 2500],
            ['name' => 'Novembre', 'sales' => 2940, 'depotage' => 2700],
            ['name' => 'Décembre', 'sales' => 3140, 'depotage' => 2900],
        ];

        $staticLabels = [];
        $staticData = [];  
        $staticDataDepotage = [];

        foreach ($productsDataStatic as $product) {
            $staticLabels[] = $product['name'];
            $staticData[] = $product['sales'];
            $staticDataDepotage[] = $product['depotage'];
        }

        $pompisteStats = [
            'pompiste' => ['nom' => $personnel->nom ?? 'Personnel Connecté', 'id' => $personnel->id ?? null],
        ];

        // Récupérer les statistiques pour le tableau et les exports
        $statistiques = $this->getStatistiquesData();

        $viewData = [
            'personnel' => $personnel,
            'pompisteStats' => $pompisteStats,
            'labels' => $staticLabels,
            'data' => $staticData,
            'dataDepotage' => $staticDataDepotage,
            'statistiques' => $statistiques,
        ];
        return view('pompiste.rapport.index', $viewData);
    }

   public function exportPdf()
    {
        $statistiques = $this->getStatistiquesData();
        $data = [
            'statistiques' => $statistiques,
            'titre' => 'Rapport des Statistiques Mensuelles',
            'dateExport' => now()->format('d/m/Y H:i')
        ];

        $pdf = Pdf::loadView('pompiste.rapport.statistiques-pdf', $data);

        return $pdf->download('statistiques-mensuelles.pdf');
    }

    public function exportExcel()
    {
        $statistiques = $this->getStatistiquesData();

        // Créez une instance de votre classe d'exportation
        return Excel::download(new StatistiquesExport($statistiques), 'statistiques-mensuelles.xlsx');
    }

    private function getStatistiquesData()
    {
        return collect([
            (object) ['mois' => 'Janvier', 'distribution' => 1540, 'depotage' => 1200],
            (object) ['mois' => 'Février', 'distribution' => 2240, 'depotage' => 2100],
            (object) ['mois' => 'Mars', 'distribution' => 1840, 'depotage' => 1600],
            (object) ['mois' => 'Avril', 'distribution' => 2040, 'depotage' => 1800],
            (object) ['mois' => 'Mai', 'distribution' => 1740, 'depotage' => 1500],
            (object) ['mois' => 'Juin', 'distribution' => 1940, 'depotage' => 1700],
            (object) ['mois' => 'Juillet', 'distribution' => 2140, 'depotage' => 1900],
            (object) ['mois' => 'Août', 'distribution' => 2340, 'depotage' => 2100],
            (object) ['mois' => 'Septembre', 'distribution' => 2540, 'depotage' => 2300],
            (object) ['mois' => 'Octobre', 'distribution' => 2740, 'depotage' => 2500],
            (object) ['mois' => 'Novembre', 'distribution' => 2940, 'depotage' => 2700],
            (object) ['mois' => 'Décembre', 'distribution' => 3140, 'depotage' => 2900],
        ]);
    }



}
