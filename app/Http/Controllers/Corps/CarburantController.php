<?php

namespace App\Http\Controllers\Corps;

use App\Http\Controllers\Controller;
use App\Models\Carburant;
use App\Models\Personnel; // Pour le select dans la modale
use App\Models\Distributeur; // Pour le select dans la modale
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CarburantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        // Récupère les transactions de carburant pour ce corps d'armée
        // avec les relations pour afficher les détails
        $carburants = Carburant::where('corps_arme_id', $userCorpsArmeId)
                            ->with(['personnel', 'distributeur.service']) // Eager load relations imbriquées
                            ->orderBy('date_transaction', 'desc') // Plus récentes en premier
                            ->paginate(15);

        // Récupère le personnel du corps d'armée pour le formulaire de la modale
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                               ->orderBy('nom', 'asc')->get();

        // Récupère les distributeurs des services de ce corps d'armée pour la modale
        $distributeurs = Distributeur::whereHas('service', function ($query) use ($userCorpsArmeId) {
                                    $query->where('corps_arme_id', $userCorpsArmeId);
                                })
                                ->orderBy('identifiant', 'asc')->get();

        return view('corpsArme.carburants.index', compact('carburants', 'personnels', 'distributeurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        $validatedData = $request->validate([
            'date_transaction' => 'required|date',
            'type_carburant' => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0.01',
            'personnel_id' => [
                'required',
                'integer',
                Rule::exists('personnels', 'id')->where(function ($query) use ($userCorpsArmeId) {
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
           'distributeur_id' => [
    'required',
    'integer',
    // On va vérifier que le distributeur_id existe dans la table 'distributeurs'
    // ET que ce distributeur est lié à un service qui appartient au corps_arme_id de l'utilisateur.
    Rule::exists('distributeurs', 'id')->where(function ($query) use ($userCorpsArmeId) {
        // Ici, $query fait référence à une requête sur la table 'distributeurs'
        // On doit s'assurer que le 'service_id' du distributeur correspond à un service
        // qui a le bon 'corps_arme_id'.
        // Pour cela, on peut utiliser une sous-requête 'whereIn' ou une jointure.
        // Utilisons whereIn avec les IDs des services du corps de l'utilisateur :

        $serviceIdsDuCorps = \App\Models\Service::where('corps_arme_id', $userCorpsArmeId)
                                                ->pluck('id'); // Récupère seulement les IDs

        // Le distributeur doit avoir un service_id qui est dans la liste des $serviceIdsDuCorps
        return $query->whereIn('service_id', $serviceIdsDuCorps);
    }),
],
            'vehicule_receveur_immat' => 'nullable|string|max:255',
            'kilometrage_receveur' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'form_type' => 'sometimes|string',
        ],[
            'date_transaction.required' => 'La date et heure sont obligatoires.',
            'type_carburant.required' => 'Le type de carburant est obligatoire.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être positive.',
            'personnel_id.required' => 'Le personnel est obligatoire.',
            'personnel_id.exists' => 'Le personnel sélectionné est invalide.',
            'distributeur_id.required' => 'Le distributeur est obligatoire.',
            'distributeur_id.exists' => 'Le distributeur sélectionné est invalide.',
        ]);

        try {
            $carburant = new Carburant();
            $carburant->fill($validatedData); // Mass assignment si $fillable est bien configuré
            $carburant->corps_arme_id = $userCorpsArmeId; // Assigner le corps de l'utilisateur connecté
            $carburant->save();

            // Optionnel : Mettre à jour le niveau_actuel du distributeur si renseigné
            // $distributeur = Distributeur::find($validatedData['distributeur_id']);
            // if ($distributeur && $distributeur->niveau_actuel !== null) {
            //     $distributeur->niveau_actuel -= $validatedData['quantite'];
            //     $distributeur->save();
            // }

            return redirect()->route('corps.carburants.index')
                             ->with('success', 'Transaction de carburant enregistrée avec succès !');

        } catch (\Exception $e) {
            // \Log::error("Erreur création transaction carburant: " . $e->getMessage());
            return redirect()->route('corps.carburants.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement de la transaction. '. $e->getMessage()])
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
