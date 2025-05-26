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
    private array $pompistesDataStatic;
    private array $cuvesData;
    private Collection $transactionsDataCollection;

    public function __construct()
    {
        
        $this->pompistesDataStatic = [
            ['id' => 1, 'pompiste_id_unique' => 'P001', 'nom' => 'Alice Martin (Fake)'],
            ['id' => 2, 'pompiste_id_unique' => 'P002', 'nom' => 'Bruno Petit (Fake)'],
            ['id' => 3, 'pompiste_id_unique' => 'P003', 'nom' => 'Chloé Dubois (Fake)'],
        ];

        $this->cuvesData = [
            'Diesel' => ['type_carburant' => 'Diesel', 'capacite_totale_litres' => 10000],
            'Kérosène' => ['type_carburant' => 'Kérosène', 'capacite_totale_litres' => 5000],
            'Essence' => ['type_carburant' => 'Essence', 'capacite_totale_litres' => 8000],
        ];

        $this->transactionsDataCollection = $this->generateFakeTransactions();
    }

    private function generateFakeTransactions(int $numDays = 90): Collection
    {
        $transactions = [];
        $startDate = Carbon::now()->subDays($numDays);
        $pompisteStaticIds = array_column($this->pompistesDataStatic, 'id');
        $fuelTypes = array_keys($this->cuvesData);

        for ($i = 0; $i < $numDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $numTransactionsJour = rand(10, 30);

            for ($j = 0; $j < $numTransactionsJour; $j++) {
                $pompisteIdForTransaction = $pompisteStaticIds[array_rand($pompisteStaticIds)];
                $fuelType = $fuelTypes[array_rand($fuelTypes)];
                $cuveInfo = $this->cuvesData[$fuelType];
                $capaciteCuveConcernee = $cuveInfo['capacite_totale_litres'];
                $actionType = (rand(1, 10) <= 9) ? 'Distribution' : 'Dépotage';

                if ($actionType == 'Distribution') {
                    $quantity = round(rand(2000, 10000) / 100, 2);
                } else {
                    $quantity = round(rand((int)($capaciteCuveConcernee * 0.2 * 100), (int)($capaciteCuveConcernee * 0.8 * 100)) / 100, 2);
                }
                $transactionTimestamp = $currentDate->copy()->addHours(rand(6, 21))->addMinutes(rand(0, 59));

                $transactions[] = [
                    'pompiste_id' => $pompisteIdForTransaction,
                    'type_carburant' => $fuelType,
                    'type_action' => $actionType,
                    'quantite_litres' => $quantity,
                    'capacite_cuve_concernee_litres' => $capaciteCuveConcernee,
                    'timestamp_action' => $transactionTimestamp->copy(),
                    'date_action_str' => $transactionTimestamp->toDateString(),
                    'mois_action_str' => $transactionTimestamp->format('Y-m'),
                    'annee_action' => $transactionTimestamp->year,
                ];
            }
        }
        return new Collection($transactions);
    }

public function rapport(Request $request)
{
    $personnel = Auth::guard('personnel_soute')->user();

    $actionsParJour = $this->transactionsDataCollection
        ->groupBy('date_action_str')
        ->map(fn ($txs) => [
            'date' => $txs->first()['timestamp_action']->toDateString(),
            'Distribution' => $txs->where('type_action', 'Distribution')->count(),
            'Dépotage' => $txs->where('type_action', 'Dépotage')->count(),
        ])->sortBy('date')->values();

    $chartDataGlobal = [
        'labels' => $actionsParJour->pluck('date')->all(),
        'datasets' => [
            [
                'label' => 'Distributions',
                'data' => $actionsParJour->pluck('Distribution')->all(),
                'borderColor' => 'rgb(75, 192, 192)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                'tension' => 0.1,
                'fill' => false,
            ],
            [
                'label' => 'Dépotages',
                'data' => $actionsParJour->pluck('Dépotage')->all(),
                'borderColor' => 'rgb(255, 99, 132)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                'tension' => 0.1,
                'fill' => false,
            ]
        ]
    ];

    $anneesForSelect = $this->transactionsDataCollection->pluck('annee_action')->unique()->sortDesc()->values();
    if ($anneesForSelect->isEmpty()) {
        $anneesForSelect->push(Carbon::now()->year);
    }
    $moisNoms = collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => Carbon::create()->month($m)->translatedFormat('F')]);

    $selectedAnnee = $request->input('annee', Carbon::now()->year);
    $selectedMoisNum = $request->input('mois');

    $idPourFiltrerTransactionsDuPersonnel = null;
    if (isset($this->pompistesDataStatic[0]['id'])) {
        $idPourFiltrerTransactionsDuPersonnel = $this->pompistesDataStatic[0]['id'];
    } else {

    }


    $transactionsDuPersonnelFictif = collect();
    if ($idPourFiltrerTransactionsDuPersonnel) {
        $transactionsDuPersonnelFictif = $this->transactionsDataCollection
            ->where('pompiste_id', $idPourFiltrerTransactionsDuPersonnel)
            ->where('annee_action', (int)$selectedAnnee);
    }


    $statsMois = null;
    if ($selectedMoisNum && !$transactionsDuPersonnelFictif->isEmpty()) {
        $transactionsMois = $transactionsDuPersonnelFictif->filter(
            fn ($t) => $t['timestamp_action']->month == (int)$selectedMoisNum
        );

        $statsMois = $this->calculateStatsForPeriod($transactionsMois, 'jour');
    }

    $statsAnnee = null;
    if (!$transactionsDuPersonnelFictif->isEmpty()) {
    
        $statsAnnee = $this->calculateStatsForPeriod($transactionsDuPersonnelFictif, 'mois');
    }

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
    // ...
];

$staticLabels = [];
$staticData = [];           // Distribution
$staticDataDepotage = [];   // Dépotage

foreach ($productsDataStatic as $product) {
    $staticLabels[] = $product['name'];
    $staticData[] = $product['sales'];
    $staticDataDepotage[] = $product['depotage'];
}


    $pompisteStats = [
        'pompiste' => ['nom' => $personnel->nom ?? 'Personnel Connecté', 'id' => $personnel->id],
        'annee' => $selectedAnnee,
        'mois' => $selectedMoisNum ? $moisNoms[(int)$selectedMoisNum] : null,
        'num_mois' => $selectedMoisNum,
        'stats_mois' => $statsMois,
        'stats_annee' => $statsAnnee,
    ];

    $viewData = [
        'personnel' => $personnel,
        'chartDataGlobal' => $chartDataGlobal,
        'anneesForSelect' => $anneesForSelect,
        'moisNoms' => $moisNoms,
        'pompisteStats' => $pompisteStats,
        'selectedAnnee' => $selectedAnnee,
        'selectedMoisNum' => $selectedMoisNum,
        'labels' => $staticLabels,
        'data' => $staticData,
        'dataDepotage' => $staticDataDepotage,
    ];

    return view('pompiste.rapport.index', $viewData);
}

    private function calculateStatsForPeriod(Collection $transactions, string $groupByPeriod): array
    {
        if ($transactions->isEmpty()) {
            return $this->emptyStats();
        }
        $keyForGrouping = ($groupByPeriod === 'jour') ? 'date_action_str' : 'mois_action_str';

        $distributions = $transactions->where('type_action', 'Distribution')
            ->groupBy($keyForGrouping)
            ->map(fn ($group, $period) => $this->aggregateByFuel($group, $period))
            ->sortBy(fn($items) => $items->first()->periode_obj->timestamp)
            ->flatMap(fn ($item) => $item)
            ->values();

        $depotages = $transactions->where('type_action', 'Dépotage')
            ->groupBy($keyForGrouping)
            ->map(fn ($group, $period) => $this->aggregateByFuel($group, $period))
            ->sortBy(fn($items) => $items->first()->periode_obj->timestamp)
            ->flatMap(fn ($item) => $item)
            ->values();

        return [
            'distributions' => $distributions,
            'depotages' => $depotages,
            'chart_distributions' => $this->prepareChartDataFromAggregated($distributions, $groupByPeriod),
            'chart_depotages' => $this->prepareChartDataFromAggregated($depotages, $groupByPeriod),
            'aggregation_level' => $groupByPeriod,
        ];
    }

    private function aggregateByFuel(Collection $transactionsInPeriod, string $periodStr): Collection
    {
        $periodeObj = (strlen($periodStr) > 7) ? Carbon::createFromFormat('Y-m-d', $periodStr)->startOfDay() : Carbon::createFromFormat('Y-m', $periodStr)->startOfMonth();
        return $transactionsInPeriod
            ->groupBy('type_carburant')
            ->map(fn (Collection $fuelGroup, $fuelType) => (object)[
                'periode_str' => $periodStr,
                'periode_obj' => $periodeObj->copy(),
                'type_carburant' => $fuelType,
                'total_quantite' => $fuelGroup->sum('quantite_litres'),
                'capacite_totale_litres' => $fuelGroup->first()['capacite_cuve_concernee_litres'],
            ])->values();
    }

    private function prepareChartDataFromAggregated(Collection $aggregatedData, string $periodType): array
    {
        if ($aggregatedData->isEmpty()) {
            return ['labels' => [], 'datasets' => []];
        }

        $labels = $aggregatedData->map(fn ($item) =>
            ($periodType === 'jour')
                ? $item->periode_obj->format('d M')
                : $item->periode_obj->translatedFormat('F')
        )->unique()->values();

        $fuelTypes = $aggregatedData->pluck('type_carburant')->unique()->values();
        $datasets = [];
        $colors = ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)'];

        foreach ($fuelTypes as $index => $fuel) {
            $fuelChartData = $labels->map(function ($labelPeriod) use ($aggregatedData, $fuel, $periodType) {
                $item = $aggregatedData->first(function ($item) use ($labelPeriod, $fuel, $periodType) {
                    if (!isset($item->periode_obj) || !isset($item->type_carburant)) {
                        return false;
                    }

                    $itemPeriodFormatted = ($periodType === 'jour' && $item->periode_obj instanceof \Carbon\Carbon)
                        ? $item->periode_obj->format('d M')
                        : ($item->periode_obj instanceof \Carbon\Carbon
                            ? $item->periode_obj->translatedFormat('F')
                            : null);

                    return $itemPeriodFormatted === $labelPeriod && $item->type_carburant === $fuel;
                });

                return $item && isset($item->total_quantite) ? $item->total_quantite : 0;
            });

            $datasets[] = [
                'label' => $fuel,
                'data' => $fuelChartData,
                'backgroundColor' => $colors[$index % count($colors)]
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }



    public function show_chart()
    {
        $productsData = [
            ['name' => 'Pommes', 'sales' => 150],
            ['name' => 'Bananes', 'sales' => 220],
            ['name' => 'Oranges', 'sales' => 180],
            ['name' => 'Fraises', 'sales' => 300],
            ['name' => 'Kiwis', 'sales' => 120],
        ];

        $labels = [];
        $data = [];

        foreach ($productsData as $product) {
            $labels[] = $product['name'];
            $data[] = $product['sales'];
        }

        return view('charts.product_sales', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }


}
