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

class MarineController extends Controller
{
    public function index()
    {
        $user = Auth::guard('corps')->user(); // L'utilisateur CorpsArme connecté
        $corpsArmeId = $user->id;

        // Vérifie que l'utilisateur est bien du corps 'marine'
        // Cette vérification est importante si la route est générique,
        // mais si la route est spécifique, elle est moins critique ici,
        // car seul un utilisateur 'Marine' devrait atteindre cette route via le handleLogin.
        // Cependant, c'est une bonne sécurité.
        if ($user->name !== 'Marine') {
            abort(403, 'Accès non autorisé à ce tableau de bord.');
        }

        // Récupérer les données spécifiques pour le dashboard Marine
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

        // IMPORTANT: Renvoie vers une vue spécifique à la Marine
        // qui utilisera son propre layout/sidebar si nécessaire.
        return view('marine.dashboard', $viewData);
    }

    public function profile()
    {
        return view('marine.profil.index');
    }
}
