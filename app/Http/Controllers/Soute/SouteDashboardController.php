<?php

// app/Http/Controllers/Soute/SouteDashboardController.php
namespace App\Http\Controllers\Soute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Soute;
use App\Models\Personnel;

class SouteDashboardController extends Controller
{
    // app/Http/Controllers/Soute/SouteDashboardController.php
// public function index(Request $request)
// {
//     $personnel = Auth::guard('personnel_soute')->user();

//     $soute = Soute::first();

//     // if (!$activeSouteId) {
//     //     // Gérer le cas où l'ID de la soute n'est pas en session (ne devrait pas arriver si le flux de login est correct)
//     //     Auth::guard('personnel_soute')->logout();
//     //     $request->session()->invalidate();
//     //     $request->session()->regenerateToken();
//     //     return redirect()->route('soute.dashboard.login');
//     // }

//     // $soute = $personnel->soute; // Ancienne méthode si une seule soute via relation directe
//     // $soute = Soute::find($activeSouteId);

//     // if (!$soute || !$personnel->soutes()->where('soutes.id', $soute->id)->exists()) { // Vérifie que le personnel est bien lié à cette soute
//     //     // Gérer le cas où la soute n'est pas trouvée ou n'est pas liée au personnel
//     //     Auth::guard('personnel_soute')->logout();
//         // ... (invalider session, etc.) ...
//     //     return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Accès à la soute non autorisé.']);
//     // }

   
//     return view('pompiste.dashboard', compact('soute','personnel'));
// }
public function index(Request $request)
{
    $personnel = Auth::guard('personnel_soute')->user();
    $activeSouteId = session('active_soute_id'); // Récupère l'ID de la soute active depuis la session

    if (!$activeSouteId) {
        Auth::guard('personnel_soute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Session de soute invalide ou expirée. Veuillez vous reconnecter.']);
    }

    // Récupérer la soute active et s'assurer que le personnel y est lié
    // Si relation Many-to-Many:
    $soute = $personnel->soutes()->find($activeSouteId);
    // Si relation One-to-Many (personnel a un soute_id):
    // $soute = Soute::where('id', $personnel->soute_id)->where('id', $activeSouteId)->first();


    if (!$soute) {
        // La soute active n'existe pas ou le personnel n'y est plus lié
        Auth::guard('personnel_soute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Effacer l'ID de session
        session()->forget('active_soute_id');
        return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Accès à la soute non autorisé ou soute introuvable.']);
    }
    $fuelsData = [
            ['type' => 'Gasoil', 'niveau_pour_affichage' => 10],
            ['type' => 'Essance', 'niveau_pour_affichage' => 20],
            ['type' => 'Kerosene', 'niveau_pour_affichage' => 30],
            // Ajoute d'autres types si nécessaire
        ];
  
    return view('pompiste.dashboard', compact('personnel', 'soute', 'fuelsData'));
}
}
