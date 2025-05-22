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

    // public function chauffeur_armee_terre()
    // {
    //     $userCorpsArmeId = Auth::guard('corps')->id();
    //     $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
    //                             ->with(['service', 'soutes'])
    //                             ->orderBy('nom', 'asc')
    //                             ->orderBy('prenom', 'asc')
    //                             ->paginate(10);

    //     // Services pour le select (si tu le gardes dans le formulaire)
    //     $services = Service::where('corps_arme_id', $userCorpsArmeId)
    //                         ->orderBy('nom', 'asc')
    //                         ->get();

    //     // Soutes pour le select multiple dans la modale de création
    //     $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
    //                     ->orderBy('nom', 'asc')
    //                     ->get();

    //     return view('armee-terre.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    // }

    // public function chauffeur_armee_air()
    // {
    //     $userCorpsArmeId = Auth::guard('corps')->id();
    //     $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
    //                             ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
    //                             ->orderBy('nom', 'asc')
    //                             ->orderBy('prenom', 'asc')
    //                             ->paginate(10);

    //     // Services pour le select (si tu le gardes dans le formulaire)
    //     $services = Service::where('corps_arme_id', $userCorpsArmeId)
    //                         ->orderBy('nom', 'asc')
    //                         ->get();

    //     // Soutes pour le select multiple dans la modale de création
    //     $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
    //                     ->orderBy('nom', 'asc')
    //                     ->get();

    //     return view('armee-air.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    // }


    // public function chauffeur_gendarmerie()
    // {
    //     $userCorpsArmeId = Auth::guard('corps')->id();
    //     $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
    //                             ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
    //                             ->orderBy('nom', 'asc')
    //                             ->orderBy('prenom', 'asc')
    //                             ->paginate(10);

    //     // Services pour le select (si tu le gardes dans le formulaire)
    //     $services = Service::where('corps_arme_id', $userCorpsArmeId)
    //                         ->orderBy('nom', 'asc')
    //                         ->get();

    //     // Soutes pour le select multiple dans la modale de création
    //     $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
    //                     ->orderBy('nom', 'asc')
    //                     ->get();

    //     return view('gendarmerie.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    // }

    // public function marine()
    // {
    //     $userCorpsArmeId = Auth::guard('corps')->id();
    //     $personnels = Personnel::where('corps_arme_id', $userCorpsArmeId)
    //                             ->with(['service', 'soutes']) // Charger la relation 'soutes' (pluriel)
    //                             ->orderBy('nom', 'asc')
    //                             ->orderBy('prenom', 'asc')
    //                             ->paginate(10);

    //     // Services pour le select (si tu le gardes dans le formulaire)
    //     $services = Service::where('corps_arme_id', $userCorpsArmeId)
    //                         ->orderBy('nom', 'asc')
    //                         ->get();

    //     // Soutes pour le select multiple dans la modale de création
    //     $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
    //                     ->orderBy('nom', 'asc')
    //                     ->get();

    //     return view('marine.personnel.chauffeur', compact('personnels', 'services', 'soutes'));
    // }


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
            'matricule' => [
                'required', 'string', 'max:255',
                Rule::unique('personnels')->where(fn ($query) => $query->where('corps_arme_id', $userCorpsArmeId))
            ],
            'email' => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique('personnels')->where(fn ($query) => $query->where('corps_arme_id', $userCorpsArmeId))
            ],
            'service_id' => [
                'nullable', 'integer',
                Rule::exists('services', 'id')->where('corps_arme_id', $userCorpsArmeId),
            ],
            'soutes_ids' => 'nullable|array', // Doit être un tableau
            'soutes_ids.*' => [ // Valide chaque ID dans le tableau
                'integer',
                Rule::exists('soutes', 'id')->where('corps_arme_id', $userCorpsArmeId), // Sécurité importante
            ],
        ]);
    
        try {
            $personnelData = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'matricule' => $request->matricule,
                'email' => $request->email,
                'corps_arme_id' => $userCorpsArmeId,
            ];
            if ($request->filled('service_id')) {
                $personnelData['service_id'] = $request->service_id;
            }
    
            $personnel = Personnel::create($personnelData);
          
            if ($request->filled('soutes_ids')) {
                $soutesToAttach = $validatedData['soutes_ids'] ?? [];
                if (!empty($soutesToAttach)) {
                    $personnel->soutes()->attach($soutesToAttach); // Utiliser attach()
                }
            }
            // Gestion de la relation Many-to-Many
            // $validatedData['soutes_ids'] contiendra uniquement les IDs valides et appartenant au corps d'armée.
            // Si 'soutes_ids' n'est pas dans la requête ou est vide, $request->soutes_ids sera null ou un tableau vide.
            // sync() gère bien les tableaux vides (détache toutes les relations).
            $soutesToSync = $validatedData['soutes_ids'] ?? [];
            $personnel->soutes()->sync($soutesToSync);
    
            return redirect()->route('corps.personnel.index')
                             ->with('success', 'Employé ajouté avec succès !');
    
        } catch (\Illuminate\Validation\ValidationException $e) { // Gérer spécifiquement les erreurs de validation
            \Log::error("Erreur de validation création personnel: " . $e->getMessage() . " Errors: " . json_encode($e->errors()));
            return redirect()->back()
                             ->withErrors($e->errors()) // Renvoyer les erreurs de validation à la vue
                             ->withInput();
        } catch (\Exception $e) { // Gérer les autres exceptions
            \Log::error("Erreur création personnel: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                             ->withErrors(['error' => 'Une erreur est survenue lors de la création. Détail: ' . $e->getMessage()])
                             ->withInput();
        }
    }

    public function update(Request $request, $id) // ou Personnel $personnel avec Route Model Binding
{
    $userCorpsArmeId = Auth::guard('corps')->id();
    // Utiliser findOrFail pour une gestion d'erreur propre si l'ID n'existe pas
    $personnel = Personnel::findOrFail($id);

    // Vérification d'appartenance
    if ($personnel->corps_arme_id !== $userCorpsArmeId) {
        return redirect()->back()->withErrors(['error' => 'Action non autorisée.']);
    }

    $validatedData = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'matricule' => [
            'required', 'string', 'max:255',
            Rule::unique('personnels')->ignore($personnel->id)->where(fn ($query) => $query->where('corps_arme_id', $userCorpsArmeId))
        ],
        'email' => [
            'nullable', 'string', 'email', 'max:255',
            Rule::unique('personnels')->ignore($personnel->id)->where(fn ($query) => $query->where('corps_arme_id', $userCorpsArmeId))
        ],
        'service_id' => [ // Assurez-vous que service_id est bien géré
            'nullable', 'integer',
            Rule::exists('services', 'id')->where('corps_arme_id', $userCorpsArmeId),
        ],
        'soutes_ids' => 'nullable|array',
        'soutes_ids.*' => [
            'integer',
            Rule::exists('soutes', 'id')->where('corps_arme_id', $userCorpsArmeId), // Sécurité
        ],
    ]);

    try {
        // Préparer les données à mettre à jour pour le modèle Personnel
        $updatePersonnelData = [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'matricule' => $validatedData['matricule'],
            'email' => $validatedData['email'],
            // Ne pas inclure corps_arme_id ici, il ne devrait pas changer lors d'un update standard
        ];
         if (array_key_exists('service_id', $validatedData)) { // Vérifier si service_id est dans les données validées
            $updatePersonnelData['service_id'] = $validatedData['service_id']; // Peut être null si validé comme nullable
        }

        $personnel->update($updatePersonnelData);

        // Gestion de la relation Many-to-Many
        $soutesToSync = $validatedData['soutes_ids'] ?? [];
        $personnel->soutes()->sync($soutesToSync);

        return redirect()->route('corps.personnel.index')
                         ->with('success', 'Employé mis à jour !');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error("Erreur de validation modification personnel: " . $e->getMessage() . " Errors: " . json_encode($e->errors()));
        return redirect()->back()
                         ->withErrors($e->errors())
                         ->withInput();
    } catch (\Exception $e) {
        \Log::error("Erreur modification personnel: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        return back()->withInput()->withErrors(['error' => 'Échec de la mise à jour. Détail: ' . $e->getMessage()]);
    }
}

public function destroy($id)
{
    $personnel = Personnel::findOrFail($id);

    if ($personnel->corps_arme_id !== Auth::guard('corps')->id()) {
        return redirect()->back()->withErrors(['error' => 'Action non autorisée.']);
    }

    try {
        $personnel->delete(); // Les entrées dans personnel_soute seront supprimées par cascade

        return redirect()->route('corps.personnel.index')
                         ->with('success', 'Employé supprimé !');

    } catch (\Exception $e) {
        \Log::error("Erreur suppression personnel: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        return back()->withErrors(['error' => 'Échec de la suppression. Détail: ' . $e->getMessage()]);
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


// Méthode destroy

public function showAssignSoutesForm(Personnel $personnel) // Utilisation du Route Model Binding
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        // S'assurer que le personnel appartient au corps d'armée de l'utilisateur connecté
        if ($personnel->corps_arme_id !== $userCorpsArmeId) {
            return redirect()->route('corps.personnel.index')->withErrors(['error' => 'Action non autorisée. Ce personnel n\'appartient pas à votre corps d\'armée.']);
        }

        // Récupérer toutes les soutes disponibles pour le corps d'armée de l'utilisateur
        $soutesDisponibles = Soute::where('corps_arme_id', $userCorpsArmeId)
                                ->orderBy('nom', 'asc')
                                ->get();

        // Récupérer les IDs des soutes actuellement assignées à ce personnel
        $soutesAssigneesIds = $personnel->soutes->pluck('id')->toArray();

        // Détermine le chemin de la vue (spécifique au corps ou par défaut)
        $viewPath = $this->getViewPath('assign_soutes'); // On aura besoin d'une vue 'assign_soutes.blade.php'

        return view($viewPath, compact('personnel', 'soutesDisponibles', 'soutesAssigneesIds'));
    }
    public function handleAssignSoutes(Request $request, Personnel $personnel)
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        // S'assurer que le personnel appartient au corps d'armée de l'utilisateur
        if ($personnel->corps_arme_id !== $userCorpsArmeId) {
            return redirect()->route('corps.personnel.index')->withErrors(['error' => 'Action non autorisée.']);
        }

        $validated = $request->validate([
            'soutes_ids'   => 'nullable|array', // Peut être vide si on veut désassigner toutes les soutes
            'soutes_ids.*' => [ // Valide chaque ID dans le tableau
                'integer',
                Rule::exists('soutes', 'id')->where(function ($query) use ($userCorpsArmeId) {
                    // S'assurer que chaque soute sélectionnée appartient bien au corps d'armée
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
        ], [
            'soutes_ids.*.exists' => 'L\'une des soutes sélectionnées est invalide ou n\'appartient pas à votre corps d\'armée.',
            'soutes_ids.*.integer' => 'Format d\'ID de soute invalide.',
        ]);

        try {
            $soutesNouvellesAAssigner = $validated['soutes_ids'] ?? [];
            if (!empty($soutesNouvellesAAssigner)) {
                // Récupérer les IDs des soutes déjà assignées pour ne pas les ré-attacher si la contrainte unique est là
                // ou pour éviter des doublons logiques si la contrainte est enlevée.
                $soutesActuellementAssigneesIds = $personnel->soutes()->pluck('soutes.id')->toArray();
                
                $idsUniquementNouveaux = array_diff($soutesNouvellesAAssigner, $soutesActuellementAssigneesIds);
    
                if(!empty($idsUniquementNouveaux)){
                     $personnel->soutes()->attach($idsUniquementNouveaux);
                }
            }
            // Cette méthode ne gère pas la désélection. Les soutes non présentes dans $soutesNouvellesAAssigner
            // mais déjà assignées resteront assignées.
    
            return redirect()->route('corps.personnel.index')
                             ->with('success', 'Nouvelles soutes assignées avec succès.');
        } catch (\Exception $e) {
            \Log::error("Erreur assignation soutes: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                             ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour des assignations.'])
                             ->withInput();
        }
    }
    

}