<?php

namespace App\Http\Controllers\Corps; // Assure-toi que le namespace est correct

use App\Http\Controllers\Controller; // Importe le Controller de base
use App\Models\Service;             // Importe le modèle Service
use Illuminate\Http\Request;         // Importe Request
use Illuminate\Support\Facades\Auth; // Importe Auth pour l'utilisateur connecté

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère l'ID du Corps d'Armée de l'utilisateur connecté
        $userCorpsArmeId = Auth::guard('corps')->id();

        // Récupère uniquement les services appartenant à ce corps d'armée
        // Ordonnés par nom et paginés (10 par page par exemple)
        $services = Service::where('corps_arme_id', $userCorpsArmeId)
                           ->orderBy('nom', 'asc')
                           ->paginate(10); // Ajuste le nombre si nécessaire

        // Retourne la vue 'index' du dossier 'services' en passant les services récupérés
        return view('corpsArme.services.index', compact('services'));
    }

    // ... Les autres méthodes (create, store, etc.) seront remplies plus tard ...

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logique à ajouter
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logique à ajouter
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) // Ou Service $service avec Route Model Binding
    {
        // Logique à ajouter (optionnel pour l'instant)
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) // Ou Service $service
    {
        // Logique à ajouter
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // Ou Service $service
    {
        // Logique à ajouter
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // Ou Service $service
    {
        // Logique à ajouter
    }
}