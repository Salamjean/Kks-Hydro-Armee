<?php

namespace App\Http\Controllers\Corps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Personnel;
use App\Models\Soute;
use App\Models\Service; // Gardé si service_id est toujours utilisé

class PersonnelController extends Controller
{
    protected function getViewPath(string $viewName): string
    {
        $user = Auth::guard('corps')->user();
        $corpsNameNormalized = strtolower(str_replace(['é', 'ê', ' '], ['e', 'e', '-'], $user->name));

        $specificView = $corpsNameNormalized . '.personnel.' . $viewName;
        $defaultView = 'corpsArme.personnel.' . $viewName;

        if (view()->exists($specificView)) {
            return $specificView;
        }
        return $defaultView;
    }

    public function chauffeur_armee_terre()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                                ->with(['service', 'soutes'])
                                ->orderBy('nom', 'asc')
                                ->orderBy('prenom', 'asc')
                                ->paginate(10);

        // Services pour le select (si tu le gardes dans le formulaire)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                            ->orderBy('nom', 'asc')
                            ->get();

        // Soutes pour le select multiple dans la modale de création
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->get();

        return view('armee-terre.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    }

    public function chauffeur_armee_air()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                                ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
                                ->orderBy('nom', 'asc')
                                ->orderBy('prenom', 'asc')
                                ->paginate(10);

        // Services pour le select (si tu le gardes dans le formulaire)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                            ->orderBy('nom', 'asc')
                            ->get();

        // Soutes pour le select multiple dans la modale de création
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->get();

        return view('armee-air.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    }


    public function chauffeur_gendarmerie()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                                ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
                                ->orderBy('nom', 'asc')
                                ->orderBy('prenom', 'asc')
                                ->paginate(10);

        // Services pour le select (si tu le gardes dans le formulaire)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                            ->orderBy('nom', 'asc')
                            ->get();

        // Soutes pour le select multiple dans la modale de création
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->get();

        return view('gendarmerie.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    }

    public function marine()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                                ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
                                ->orderBy('nom', 'asc')
                                ->orderBy('prenom', 'asc')
                                ->paginate(10);

        // Services pour le select (si tu le gardes dans le formulaire)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                            ->orderBy('nom', 'asc')
                            ->get();

        // Soutes pour le select multiple dans la modale de création
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->get();

        return view('marine.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    }


    public function index()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
                            ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
                            ->orderBy('nom', 'asc')
                            ->orderBy('prenom', 'asc')
                            ->paginate(10);

        // Services pour le select (si tu le gardes dans le formulaire)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                           ->orderBy('nom', 'asc')
                           ->get();

        // Soutes pour le select multiple dans la modale de création
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                         ->orderBy('nom', 'asc')
                         ->get();

        return view($this->getViewPath('index'), compact('personnels', 'services', 'soutes'));
    }

    public function store(Request $request)
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => 'required|string|max:255', // Supprimer Rule::unique
            'email' => 'nullable|string|email|max:255', // Supprimer Rule::unique
            'service_id' => [
                'nullable','integer',
                Rule::exists('services', 'id')->where('corps_arme_id', $userCorpsArmeId),
            ],
            'soutes_ids' => 'nullable|array',
            'soutes_ids.*' => [
                'integer',
                Rule::exists('soutes', 'id')->where('corps_arme_id', $userCorpsArmeId),
            ],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'matricule.required' => 'Le matricule est obligatoire.',
            // SUPPRIMER LES LIGNES D'ERREUR POUR L'UNICITÉ
            'soutes_ids.*.exists' => 'Sélection de soute invalide.',
        ]);

        try {
            // Préparer les données pour la création du personnel (sans soutes_ids pour l'instant)
            $personnelData = $request->only(['nom', 'prenom', 'matricule', 'email', 'service_id']);
            $personnelData['corps_arme_id'] = $userCorpsArmeId;
            $personnelData['password'] = null; // Le mot de passe sera défini par le personnel

            $personnel = Personnel::create($personnelData);

            // Attacher les soutes sélectionnées
            if ($request->filled('soutes_ids') && is_array($request->soutes_ids)) {
                // Double-vérification que les soutes appartiennent bien au corps (déjà fait par la validation mais plus sûr)
                $soutesValidesPourAttachement = Soute::where('corps_arme_id', $userCorpsArmeId)
                                                     ->whereIn('id', $request->soutes_ids)
                                                     ->pluck('id'); // On ne récupère que les IDs valides
                if ($soutesValidesPourAttachement->isNotEmpty()) {
                    $personnel->soutes()->attach($soutesValidesPourAttachement->all());
                }
            }

            return redirect()->route('corps.personnel.index')
                             ->with('success', 'Employé "' . $personnel->nom_complet . '" ajouté avec succès !');

        } catch (\Exception $e) {
            \Log::error("Erreur création personnel: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('corps.personnel.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout de l\'employé. Veuillez vérifier les informations et réessayer.'])
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // À implémenter si besoin
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) // Ou Personnel $personnel avec Route Model Binding
    {
        // À implémenter
        // $personnel = Personnel::with('soutes')->findOrFail($id);
        // $userCorpsArmeId = Auth::guard('corps')->id();
        // $soutesDisponibles = Soute::where('corps_arme_id', $userCorpsArmeId)->orderBy('nom')->get();
        // $servicesDisponibles = Service::where('corps_arme_id', $userCorpsArmeId)->orderBy('nom')->get();
        // return view($this->getViewPath('edit'), compact('personnel', 'soutesDisponibles', 'servicesDisponibles'));
    }

    /**
     * Update the specified resource in storage.
     */
   // Méthode update
public function update(Request $request, $id)
{
    $userCorpsArmeId = Auth::guard('corps')->id();
    $personnel = Personnel::findOrFail($id);

    // Vérification d'appartenance au corps
    if ($personnel->corps_arme_id !== $userCorpsArmeId) {
        return redirect()->back()->withErrors(['error' => 'Action non autorisée.']);
    }

    $validatedData = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'matricule' => 'required|string|max:255',
        'email' => 'nullable|string|email|max:255',
        'soutes_ids' => 'nullable|array',
        'soutes_ids.*' => 'integer|exists:soutes,id'
    ]);

    try {
        // Mise à jour des données
        $personnel->update($validatedData);
        
        // Synchronisation des soutes
        if ($request->has('soutes_ids')) {
            $personnel->soutes()->sync($request->soutes_ids);
        } else {
            $personnel->soutes()->detach();
        }

        return redirect()->route('corps.personnel.index')
                         ->with('success', 'Employé mis à jour !');

    } catch (\Exception $e) {
        \Log::error("Erreur modification personnel: " . $e->getMessage());
        return back()->withInput()->withErrors(['error' => 'Échec de la mise à jour.']);
    }
}

// Méthode destroy
public function destroy($id)
{
    $personnel = Personnel::findOrFail($id);
    
    // Vérification d'appartenance
    if ($personnel->corps_arme_id !== Auth::guard('corps')->id()) {
        return redirect()->back()->withErrors(['error' => 'Action non autorisée.']);
    }

    try {
        $personnel->soutes()->detach();
        $personnel->delete();
        return redirect()->route('corps.personnel.index')
                         ->with('success', 'Employé supprimé !');

    } catch (\Exception $e) {
        \Log::error("Erreur suppression personnel: " . $e->getMessage());
        return back()->withErrors(['error' => 'Échec de la suppression.']);
    }
}
}