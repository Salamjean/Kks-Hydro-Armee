<?php

namespace App\Http\Controllers\Corps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- CETTE LIGNE EST IMPORTANTE ET DOIT ÊTRE PRÉSENTE
use Illuminate\Validation\Rule;     // Tu l'auras besoin pour la méthode store
use App\Models\Personnel;
use App\Models\Service;

class PersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Maintenant, Auth::guard() devrait fonctionner correctement
        $userCorpsArmeId = Auth::guard('corps')->id();

        // Récupère le personnel appartenant à ce corps d'armée
        // avec la relation 'service' pour afficher le nom du service
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                            ->with('service') // Eager load la relation service
                            ->orderBy('nom', 'asc')
                            ->orderBy('prenom', 'asc')
                            ->paginate(10);

        // Récupère les services du corps d'armée pour le formulaire de la modale
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                           ->orderBy('nom', 'asc')
                           ->get();

        return view('corpsArme.personnel.index', compact('personnels', 'services'));
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => [
                'required',
                'string',
                'max:255',
                // Matricule doit être unique pour ce corps d'armée
                Rule::unique('personnels')->where(function ($query) use ($userCorpsArmeId) {
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                // Email doit être unique (globalement ou par corps, à décider)
                // Pour l'instant, unique globalement s'il est fourni
                Rule::unique('personnels')->ignore($request->id), // Ignorer l'id courant en cas d'update
            ],
            'service_id' => [
                'nullable',
                'integer',
                // Le service_id doit exister et appartenir au corps d'armée de l'utilisateur
                Rule::exists('services', 'id')->where(function ($query) use ($userCorpsArmeId) {
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
            'form_type' => 'sometimes|string',
        ],[
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique' => 'Ce matricule est déjà utilisé dans votre corps d\'armée.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'service_id.exists' => 'Le service sélectionné est invalide.',
        ]);

        try {
            $personnel = new Personnel();
            $personnel->nom = $validatedData['nom'];
            $personnel->prenom = $validatedData['prenom'];
            $personnel->matricule = $validatedData['matricule'];
            $personnel->email = $validatedData['email'] ?? null; // Mettre à null si vide
            $personnel->service_id = $validatedData['service_id'] ?? null; // Mettre à null si non sélectionné
            $personnel->corps_arme_id = $userCorpsArmeId; // Assigner le corps de l'utilisateur connecté
            $personnel->save();

            return redirect()->route('corps.personnel.index')
                             ->with('success', 'Employé "' . $personnel->nom_complet . '" ajouté avec succès !');

        } catch (\Exception $e) {
            // \Log::error("Erreur création personnel: " . $e->getMessage());
            return redirect()->route('corps.personnel.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout de l\'employé.'])
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
