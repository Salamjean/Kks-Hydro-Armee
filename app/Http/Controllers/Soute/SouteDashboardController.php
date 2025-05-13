<?php

// app/Http/Controllers/Soute/SouteDashboardController.php
namespace App\Http\Controllers\Soute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Soute;

class SouteDashboardController extends Controller
{
    // app/Http/Controllers/Soute/SouteDashboardController.php
public function index(Request $request)
{
    $personnel = Auth::guard('personnel_soute')->user();

    $soute = Soute::first();

    // if (!$activeSouteId) {
    //     // Gérer le cas où l'ID de la soute n'est pas en session (ne devrait pas arriver si le flux de login est correct)
    //     Auth::guard('personnel_soute')->logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('soute.dashboard.login');
    // }

    // $soute = $personnel->soute; // Ancienne méthode si une seule soute via relation directe
    // $soute = Soute::find($activeSouteId);

    // if (!$soute || !$personnel->soutes()->where('soutes.id', $soute->id)->exists()) { // Vérifie que le personnel est bien lié à cette soute
    //     // Gérer le cas où la soute n'est pas trouvée ou n'est pas liée au personnel
    //     Auth::guard('personnel_soute')->logout();
        // ... (invalider session, etc.) ...
    //     return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Accès à la soute non autorisé.']);
    // }

   
    return view('pompiste.dashboard', compact('soute','personnel'));
}

}
