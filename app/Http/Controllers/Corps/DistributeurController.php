<?php

namespace App\Http\Controllers\Corps;
use App\Models\Distributeur;
use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class DistributeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userCorpsArmeId = Auth::guard('corps')->id(); // Assumant que l'utilisateur a un corps_arme_id
                                                                        // Ou si l'utilisateur EST le corps : Auth::guard('corps')->id();

        // Récupère les distributeurs appartenant aux services de ce corps d'armée
        $distributeurs = Distributeur::whereHas('service', function ($query) use ($userCorpsArmeId) {
                                $query->where('corps_arme_id', $userCorpsArmeId);
                            })
                            ->with('service') // Eager load la relation service
                            ->orderBy('identifiant', 'asc')
                            ->paginate(10);

        // Récupère les services du corps d'armée pour le formulaire de la modale
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                           ->orderBy('nom', 'asc')
                           ->get();

        return view('corpsArme.distributeurs.index', compact('distributeurs', 'services'));
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
        $userCorpsArmeId = Auth::guard('corps')->id(); // Ou Auth::guard('corps')->id();

        $validatedData = $request->validate([
            'identifiant' => [
                'required',
                'string',
                'max:255',
                // L'identifiant doit être unique globalement pour l'instant.
                // Si besoin d'unicité par corps, la logique serait plus complexe (via service)
                Rule::unique('distributeurs'),
            ],
            'type' => 'required|string|max:255',
            'capacite' => 'nullable|numeric|min:0',
            'niveau_actuel' => 'nullable|numeric|min:0' . ($request->capacite ? '|lte:capacite' : ''), // Ne peut pas être > capacité
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->where(function ($query) use ($userCorpsArmeId) {
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
            'form_type' => 'sometimes|string',
        ],[
            'identifiant.required' => 'L\'identifiant est obligatoire.',
            'identifiant.unique' => 'Cet identifiant est déjà utilisé.',
            'type.required' => 'Le type de distributeur est obligatoire.',
            'capacite.numeric' => 'La capacité doit être un nombre.',
            'capacite.min' => 'La capacité doit être positive.',
            'niveau_actuel.numeric' => 'Le niveau actuel doit être un nombre.',
            'niveau_actuel.min' => 'Le niveau actuel doit être positif.',
            'niveau_actuel.lte' => 'Le niveau actuel ne peut pas dépasser la capacité.',
            'service_id.required' => 'Le service de rattachement est obligatoire.',
            'service_id.exists' => 'Le service sélectionné est invalide.',
        ]);

        try {
            $distributeur = new Distributeur();
            $distributeur->identifiant = $validatedData['identifiant'];
            $distributeur->type = $validatedData['type'];
            $distributeur->capacite = $validatedData['capacite'] ?? null;
            $distributeur->niveau_actuel = $validatedData['niveau_actuel'] ?? null;
            $distributeur->service_id = $validatedData['service_id'];
            // Le corps_arme_id n'est pas direct sur le distributeur, mais via le service.
            $distributeur->save();

            return redirect()->route('corps.distributeurs.index')
                             ->with('success', 'Distributeur "' . $distributeur->identifiant . '" ajouté avec succès !');

        } catch (\Exception $e) {
            // \Log::error("Erreur création distributeur: " . $e->getMessage());
            return redirect()->route('corps.distributeurs.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout du distributeur.'])
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
