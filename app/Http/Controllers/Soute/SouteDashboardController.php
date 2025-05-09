<?php

// app/Http/Controllers/Soute/SouteDashboardController.php
namespace App\Http\Controllers\Soute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SouteDashboardController extends Controller
{
    public function index()
    {
        $personnel = Auth::guard('personnel_soute')->user();
        $soute = $personnel->soute; // Récupère la soute via la relation

        // Tu peux récupérer d'autres infos ici liées à la soute pour le dashboard
        // Ex: $distributeursDeLaSoute = $soute->distributeurs;
        // Ex: $transactionsRecentesDeLaSoute = ...

        return view('soute.dashboard', compact('personnel', 'soute')); // Vue à créer
    }
}
