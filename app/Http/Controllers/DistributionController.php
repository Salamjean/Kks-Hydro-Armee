<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Soute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // Important pour utiliser Validator::make

class DistributionController extends Controller
{
    public function store(Request $request)
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
}