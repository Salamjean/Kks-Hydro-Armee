<?php

namespace App\Http\Controllers\Pompiste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Personnel;
use App\Models\Soute;
use App\Models\Distributeur;
use App\Models\Carburant;


class PompisteController extends Controller
{
    public function distribution(Request $request)
    {
        $personnel = Auth::guard('personnel_soute')->user();
        if (!$personnel) {
            return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Veuillez vous connecter.']);
        }

        $activeSouteId = session('active_soute_id');

        if (!$activeSouteId) {
            return redirect()->route('soute.dashboard.index') // Redirige vers le dashboard principal de la soute
                             ->withErrors(['error' => 'Aucune soute active n\'a été sélectionnée. Veuillez retourner au tableau de bord.']);
        }

        // Récupérer la soute et s'assurer que le personnel y est lié
        $soute = $personnel->soutes()->find($activeSouteId);

        if (!$soute) {
            // Si la soute n'est pas trouvée ou si le personnel n'y est pas lié via la relation soutes()
            // Déconnecter et rediriger peut être une mesure de sécurité
            Auth::guard('personnel_soute')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            session()->forget('active_soute_id');
            return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Soute non trouvée ou accès non autorisé.']);
        }
  // dd($soute->types_carburants_stockes, $soute->niveau_actuel_essence, $soute->niveau_actuel_kerozen, $soute->niveau_actuel_diesel);
        // La vue 'pompiste.services.distribution' recevra l'objet $soute et $personnel
        return view('pompiste.services.distribution', compact('personnel', 'soute'));
    }

    public function depotage(Request $request)
    {
        // Logique pour la page de dépotage, similaire pour récupérer la soute si nécessaire
        $personnel = Auth::guard('personnel_soute')->user();
        $activeSouteId = session('active_soute_id');
        if (!$activeSouteId) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Aucune soute active.']);
        }
        $soute = $personnel->soutes()->find($activeSouteId);
        if (!$soute) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Soute non trouvée ou accès non autorisé.']);
        }
        return view('pompiste.services.depotage', compact('personnel', 'soute'));
    }

    public function rapport(Request $request)
    {
        // Logique pour la page de rapport
        $personnel = Auth::guard('personnel_soute')->user();
        $activeSouteId = session('active_soute_id');
        if (!$activeSouteId) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Aucune soute active.']);
        }
        $soute = $personnel->soutes()->find($activeSouteId);
         if (!$soute) {
            return redirect()->route('soute.dashboard.index')->withErrors(['error' => 'Soute non trouvée ou accès non autorisé.']);
        }
        // Vous voudrez probablement récupérer les distributions pour cette soute ici
        // $distributions = Distribution::where('soute_id', $soute->id)->latest()->paginate(15);
        return view('pompiste.rapport', compact('personnel', 'soute' /*, 'distributions' */));
    }
}
