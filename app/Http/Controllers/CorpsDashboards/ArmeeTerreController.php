<?php

namespace App\Http\Controllers\CorpsDashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Importe les modèles dont tu auras besoin pour les stats
use App\Models\Service;
use App\Models\Personnel;
use App\Models\Soute;
use App\Models\Distributeur;
use App\Models\Carburant;

class ArmeeTerreController extends Controller
{
    public function index()
    {
        $user = Auth::guard('corps')->user();
        $corpsArmeId = $user->id;

        if ($user->name !== 'Armée-Terre') {
            abort(403, 'Accès non autorisé à ce tableau de bord.');
        }

        $souteCount = Soute::where('corps_arme_id', $corpsArmeId)->count();
        $personnelCount = Personnel::where('corps_arme_id', $corpsArmeId)->count();
        $recentTransactions = Carburant::where('corps_arme_id', $corpsArmeId)
                                    ->with(['personnel', 'distributeur.soute'])
                                    ->latest('date_transaction')
                                    ->take(5)
                                    ->get();

        $viewData = [
            'user' => $user,
            'souteCount' => $souteCount,
            'personnelCount' => $personnelCount,
            'recentTransactions' => $recentTransactions,
            // ...
        ];

        // IMPORTANT: Renvoie vers une vue spécifique à la Armée-Terre
        // qui utilisera son propre layout/sidebar si nécessaire.
        return view('armee-terre.dashboard', $viewData); 
    }

    public function profile()
    {
        return view('armee-terre.profil.index');
    }
}
