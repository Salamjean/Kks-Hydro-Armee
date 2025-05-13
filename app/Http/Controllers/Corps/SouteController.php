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
        $corpsNameLower = strtolower(str_replace(['é', ' '], ['e', '-'], $user->name)); // ex: armee-air, gendarmerie

        // Vérifie si une vue spécifique au corps existe, sinon utilise une vue par défaut
        // sous 'corpsArme' ou une autre structure que tu définis.
        $specificView = $corpsNameLower . '.soutes.' . $viewName;
        $defaultView = 'corpsArme.soutes.' . $viewName; // Vue par défaut que tu avais

        if (view()->exists($specificView)) {
            return $specificView;
        }
        return $defaultView; // Fallback si la vue spécifique n'existe pas
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
            'type_carburant_principal' => 'required|string|in:Diesel,Kerozen,Essence',
            'capacite_totale' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'form_type' => 'sometimes|string',
        ], [
            'nom.required' => 'Le nom de la soute est obligatoire.',
            'nom.unique' => 'Une soute avec ce nom existe déjà pour votre corps d\'armée.',
            'capacite_totale.numeric' => 'La capacité totale doit être un nombre.',
            'type_carburant_principal.required' => 'Le type de carburant principal est obligatoire.',
            'type_carburant_principal.in' => 'Le type de carburant sélectionné est invalide.',
        ]);

        try {
            $soute = new Soute();
            $soute->fill($validatedData);
            $soute->corps_arme_id = $userCorpsArmeId;
            $soute->save();

            return redirect()->route('corps.soutes.index') // La route reste la même
                             ->with('success', 'Soute "' . $soute->nom . '" (Matricule: ' . $soute->matricule_soute . ') ajoutée avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('corps.soutes.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout de la soute.'])
                             ->withInput();
        }
    }
    // ... autres méthodes edit, update, destroy à adapter de manière similaire pour le return view()
}