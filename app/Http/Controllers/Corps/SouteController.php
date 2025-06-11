<?php

namespace App\Http\Controllers\Corps;

use App\Http\Controllers\Controller;
use App\Models\Soute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SouteController extends Controller
{
    protected function getViewPath(string $viewName): string
    {
        $user = Auth::guard('corps')->user();
        $corpsNameLower = strtolower(str_replace(['é', ' '], ['e', '-'], $user->name));

        $specificView = $corpsNameLower . '.soutes.' . $viewName;
        $defaultView = 'corpsArme.soutes.' . $viewName; 

        if (view()->exists($specificView)) {
            return $specificView;
        }
        return $defaultView;
    }

    public function index()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->paginate(10);

        return view($this->getViewPath('index'), compact('soutes'));
    }

    public function store(Request $request)
    {
        $userCorpsArmeId = Auth::guard('corps')->id();

        $validatedData = $request->validate([
            'nom' => [
                'required','string','max:255',
                Rule::unique('soutes')->where(function ($query) use ($userCorpsArmeId) {
                    return $query->where('corps_arme_id', $userCorpsArmeId);
                }),
            ],
            'localisation' => 'nullable|string|max:255',
            'type_carburants'    => 'required|array|min:1', // Doit être un tableau et avoir au moins un type
            'type_carburants.*'  => 'required|string|in:Diesel,Kerozen,Essence', // Chaque type doit être valide
            'capacite_diesel'  => 'nullable|required_if:type_carburants.*,Diesel|numeric|min:0',
            'capacite_kerozen' => 'nullable|required_if:type_carburants.*,Kerozen|numeric|min:0',
            'capacite_essence' => 'nullable|required_if:type_carburants.*,Essence|numeric|min:0',
            'description' => 'nullable|string',
            'form_type' => 'sometimes|string',
            'niveau_actuel_diesel' => 'nullable|required_if:type_carburants.*,Diesel|numeric|min:0|lte:capacite_diesel',
        'niveau_actuel_kerozen' => 'nullable|required_if:type_carburants.*,Kerozen|numeric|min:0|lte:capacite_kerozen',
        'niveau_actuel_essence' => 'nullable|required_if:type_carburants.*,Essence|numeric|min:0|lte:capacite_essence',
        'seuil_alert_diesel' => 'nullable|required_if:type_carburants.*,Diesel|numeric|min:0|lte:capacite_diesel',
        'seuil_alert_kerozen' => 'nullable|required_if:type_carburants.*,Kerozen|numeric|min:0|lte:capacite_kerozen',
        'seuil_alert_essence' => 'nullable|required_if:type_carburants.*,Essence|numeric|min:0|lte:capacite_essence',
        ], [
            'nom.required' => 'Le nom de la soute est obligatoire.',
            'nom.unique' => 'Une soute avec ce nom existe déjà pour votre corps d\'armée.',
            'type_carburants.required' => 'Veuillez sélectionner au moins un type de carburant.',
            'type_carburants.min' => 'Veuillez sélectionner au moins un type de carburant.',
            'type_carburants.*.in' => 'L\'un des types de carburant sélectionnés est invalide.',
            'capacite_diesel.required_if' => 'La capacité pour le Diesel est requise si ce type est sélectionné.',
            'capacite_kerozen.required_if' => 'La capacité pour le Kérosène est requise si ce type est sélectionné.',
            'capacite_essence.required_if' => 'La capacité pour l\'Essence est requise si ce type est sélectionné.',
            '*.numeric' => 'La capacité doit être un nombre.',
            '*.min' => 'La capacité doit être positive.',
            'niveau_actuel_diesel.required_if' => 'Le niveau actuel pour le Diesel est requis.',
        'niveau_actuel_kerozen.required_if' => 'Le niveau actuel pour le Kérosène est requis.',
        'niveau_actuel_essence.required_if' => 'Le niveau actuel pour l\'Essence est requis.',
        'seuil_alert_diesel.required_if' => 'Le seuil d\'alerte pour le Diesel est requis.',
        'seuil_alert_kerozen.required_if' => 'Le seuil d\'alerte pour le Kérosène est requis.',
        'seuil_alert_essence.required_if' => 'Le seuil d\'alerte pour l\'Essence est requis.',
        '*.lte' => 'La valeur ne peut pas dépasser la capacité totale.',
        ]);

            try {
                $soute = new Soute();
                $soute->nom = $validatedData['nom'];
                $soute->localisation = $validatedData['localisation'] ?? null;
                $soute->description = $validatedData['description'] ?? null;
                $soute->corps_arme_id = $userCorpsArmeId;

                // Stocker les types de carburants sélectionnés
                $soute->types_carburants_stockes = $validatedData['type_carburants'];

                // Stocker les capacités si les types correspondants sont sélectionnés
                $soute->capacite_diesel = in_array('Diesel', $validatedData['type_carburants']) ? ($validatedData['capacite_diesel'] ?? null) : null;
                $soute->capacite_kerozen = in_array('Kerozen', $validatedData['type_carburants']) ? ($validatedData['capacite_kerozen'] ?? null) : null;
                $soute->capacite_essence = in_array('Essence', $validatedData['type_carburants']) ? ($validatedData['capacite_essence'] ?? null) : null;
 // Niveaux actuels
 $soute->niveau_actuel_diesel = in_array('Diesel', $validatedData['type_carburants']) 
 ? ($validatedData['niveau_actuel_diesel'] ?? null) 
 : null;

$soute->niveau_actuel_kerozen = in_array('Kerozen', $validatedData['type_carburants']) 
 ? ($validatedData['niveau_actuel_kerozen'] ?? null) 
 : null;

$soute->niveau_actuel_essence = in_array('Essence', $validatedData['type_carburants']) 
 ? ($validatedData['niveau_actuel_essence'] ?? null) 
 : null;

// Seuils d'alerte
$soute->seuil_alert_diesel = in_array('Diesel', $validatedData['type_carburants']) 
 ? ($validatedData['seuil_alert_diesel'] ?? null) 
 : null;

$soute->seuil_alert_kerozen = in_array('Kerozen', $validatedData['type_carburants']) 
 ? ($validatedData['seuil_alert_kerozen'] ?? null) 
 : null;

$soute->seuil_alert_essence = in_array('Essence', $validatedData['type_carburants']) 
 ? ($validatedData['seuil_alert_essence'] ?? null) 
 : null;
                // matricule_soute sera généré par l'événement 'creating'
                $soute->save();

                return redirect()->route('corps.soutes.index')
                                ->with('success', 'Soute "' . $soute->nom . '" (Matricule: ' . $soute->matricule_soute . ') ajoutée avec succès !');
            } catch (\Exception $e) {
                \Log::error("Erreur création soute: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                return redirect()->route('corps.soutes.index')
                                ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout de la soute. ' . $e->getMessage()])
                                ->withInput();
            }
    }
}