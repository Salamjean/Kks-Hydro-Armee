<?php

namespace App\Http\Controllers\Corps;

use App\Http\Controllers\Controller;
use App\Models\Soute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SouteController extends Controller
{
    public function index()
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
        $soutes = Soute::where('corps_arme_id', $userCorpsArmeId)
                        ->orderBy('nom', 'asc')
                        ->paginate(10);
        return view('corpsArme.soutes.index', compact('soutes'));
    }

    public function store(Request $request)
    {
        $userCorpsArmeId = Auth::guard('corps')->id();
    
        $validatedData = $request->validate([
            'nom' => [ /* ... */ ],
            'localisation' => 'nullable|string|max:255',
            'type_carburant_principal' => 'required|string|in:Diesel,Kerozen,Essence', // <<--- VALIDATION POUR LE SELECT
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
            $soute->corps_arme_id = $userCorpsArmeId; // Assigner AVANT save() pour que le boot() y ait accès
            // matricule_soute sera généré par l'événement 'creating' dans le modèle Soute
            $soute->save();
    
            return redirect()->route('corps.soutes.index')
                             ->with('success', 'Soute "' . $soute->nom . '" (Matricule: ' . $soute->matricule_soute . ') ajoutée avec succès !');
        } catch (\Exception $e) {
            // Pour le débogage, tu peux logger l'erreur complète
            // \Log::error("Erreur création soute: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('corps.soutes.index')
                             ->withErrors(['error' => 'Une erreur est survenue lors de l\'ajout de la soute.'])
                             ->withInput();
        }
    }
    // Les méthodes edit, update, destroy seront à implémenter plus tard
}