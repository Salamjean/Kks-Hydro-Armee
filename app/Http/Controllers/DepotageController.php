<?php

namespace App\Http\Controllers; // Ajustez le namespace si vous l'avez mis ailleurs

use App\Http\Controllers\Controller;
use App\Models\Depotage;
use App\Models\Soute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepotageController extends Controller
{
    /**
     * La méthode pour afficher la page de dépotage est gérée par PompisteController@depotage
     * Cette classe se concentre sur le store.
     * Si vous voulez une méthode index pour lister les dépotages (hors soute), vous pouvez l'ajouter.
     */

    /**
     * Store a newly created depotage in storage.
     */
    public function store(Request $request)
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
}