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

class ArmeeAirController extends Controller
{
    public function index()
    {
        $user = Auth::guard('corps')->user(); // L'utilisateur CorpsArme connecté
        $corpsArmeId = $user->id;

        if ($user->name !== 'Armée-Air') {
            abort(403, 'Accès non autorisé à ce tableau de bord.');
        }

        // Récupérer les données spécifiques pour le dashboard Armée-Air
        $souteCount = Soute::where('corps_arme_id', $corpsArmeId)->count();
        $personnelCount = Personnel::where('corps_arme_id', $corpsArmeId)->count();
        // ... autres statistiques ...
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

        // IMPORTANT: Renvoie vers une vue spécifique à la Armée-air
        // qui utilisera son propre layout/sidebar si nécessaire.
        return view('armee-air.dashboard', $viewData);
    }
    public function profile()
    {
        return view('armee-air.profil.index');
    }
}
