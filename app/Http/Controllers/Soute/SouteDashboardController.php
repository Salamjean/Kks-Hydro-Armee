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
        $activeSouteId = session('active_soute_id');

        if (!$activeSouteId) {
            Auth::guard('personnel_soute')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('soute.dashboard.login')
                ->withErrors(['error' => 'Session de soute invalide ou expirée. Veuillez vous reconnecter.']);
        }

        // Récupérer la soute active et vérifier que le personnel y est lié
        $soute = $personnel->soutes()->find($activeSouteId);
        if (!$soute) {
            Auth::guard('personnel_soute')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            session()->forget('active_soute_id');
            return redirect()->route('soute.dashboard.login')
                ->withErrors(['error' => 'Accès à la soute non autorisé ou soute introuvable.']);
        }

        // Construire fuelsData en tant qu’objets stdClass pour la vue
        $fuelsData = [];
        if (is_array($soute->types_carburants_stockes)) {
            foreach ($soute->types_carburants_stockes as $typeRaw) {
                // On s'attend à des chaînes 'Diesel', 'Essence', 'Kerozen' (ou variantes).
                $typeLower = strtolower($typeRaw);
                // Récupérer niveau actuel ; on suppose des attributs `niveau_actuel_<typeLower>`
                $champNiveau = 'niveau_actuel_' . $typeLower;
                $champCapacite = 'capacite_' . $typeLower;
                $niveauActuel = null;
                if (isset($soute->{$champNiveau}) && $soute->{$champNiveau} !== null) {
                    $niveauActuel = (float) $soute->{$champNiveau};
                } elseif (isset($soute->{$champCapacite})) {
                    $niveauActuel = (float) $soute->{$champCapacite};
                } else {
                    $niveauActuel = 0;
                }
                // Récupérer capacité totale si existant
                $capaciteTotale = isset($soute->{$champCapacite}) ? (float)$soute->{$champCapacite} : 0;
                // Déterminer icon_class selon typeRaw ; adapte selon tes icônes réelles
                $iconClass = '';
                if (strtolower($typeRaw) === 'diesel' || strtolower($typeRaw) === 'gasoil') {
                    $iconClass = 'bi bi-truck text-primary';
                } elseif (strtolower($typeRaw) === 'essence') {
                    $iconClass = 'bi bi-car-front-fill text-success';
                } elseif (in_array(strtolower($typeRaw), ['kerozen','kerosene','kérozène'])) {
                    $iconClass = 'bi bi-airplane-engines-fill text-info';
                } else {
                    // par défaut
                    $iconClass = 'bi bi-droplet';
                }
                $fuelsData[] = (object)[
                    'type' => $typeRaw,
                    'capacite_totale' => $capaciteTotale,
                    'niveau_pour_affichage' => $niveauActuel,
                    'icon_class' => $iconClass,
                ];
            }
        }
        // Passe à la vue
        return view('pompiste.dashboard', compact('personnel', 'soute', 'fuelsData'));
    }
}
