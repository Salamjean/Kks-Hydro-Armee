<?php

namespace App\Http\Controllers\Soute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Personnel;
use App\Models\Soute;
use Illuminate\Support\Facades\Log; // Ajouter en haut avec les autres imports
use Illuminate\Validation\ValidationException;

class SoutePersonnelLoginController extends Controller
{
    // public function __construct()
    // {
    //     // Le middleware guest s'assure que seuls les utilisateurs non connectés
    //     // peuvent accéder aux formulaires de login et de traitement du login.
    //     $this->middleware('guest:personnel_soute')->except(['logout', 'showSetPasswordForm', 'setPassword']);
    // }

    public function showLoginForm()
    {
        return view('pompiste.auth.login');
    }

    public function getPersonnelSouteInfo(Request $request)
    {
        $request->validate(['email_or_matricule' => 'required|string']);

        $personnel = Personnel::where(function ($query) use ($request) {
            $query->where('email', $request->email_or_matricule)
                  ->orWhere('matricule', $request->email_or_matricule);
        })
        ->with(['soutes' => function($query) { // Chargement explicite
            $query->select('soutes.id', 'nom', 'matricule_soute');
        }])
        ->first();
        // === SECTION DE DÉBOGAGE AVEC LOG (NON BLOQUANT) - OPTIONNEL ===
        if ($personnel) {
            Log::info('SouteInfo - Personnel Trouvé:', [
                'nom' => $personnel->nom,
                'soutes_count' => $personnel->soutes ? $personnel->soutes->count() : 0,
                'soutes_data' => $personnel->soutes ? $personnel->soutes->map(function($soute){
                    return ['id' => $soute->id, 'nom' => $soute->nom, 'matricule_soute' => $soute->matricule_soute];
                })->toArray() : []
            ]);
        } else {
            Log::info('SouteInfo - Personnel Non Trouvé:', ['input' => $request->email_or_matricule]);
        }
        // ==============================================================

        if ($personnel) {
            if ($personnel->soutes && $personnel->soutes->count() > 0) {
                if ($personnel->soutes->count() === 1) {
                    $soute = $personnel->soutes->first();
                    return response()->json([
                        'success' => true,
                        'soutes' => [['id' => $soute->id, 'matricule_soute' => $soute->matricule_soute, 'nom' => $soute->nom]],
                        'multiple_soutes' => false,
                    ]);
                } else { // Plus d'une soute
                    $soutesData = $personnel->soutes->map(function ($soute) {
                        return ['id' => $soute->id, 'matricule_soute' => $soute->matricule_soute, 'nom' => $soute->nom];
                    });
                    return response()->json([
                        'success' => true,
                        'soutes' => $soutesData,
                        'multiple_soutes' => true,
                    ]);
                }
            }
            return response()->json(['success' => false, 'message' => 'Aucune soute n\'est assignée à ce personnel.']);
        }
        return response()->json(['success' => false, 'message' => 'Personnel non trouvé.']);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email_or_matricule' => 'required|string',
            'soute_id_selected' => 'required_if:multiple_soutes_found,true|nullable|integer|exists:soutes,id',
            'matricule_soute' => 'required_without:soute_id_selected|nullable|string|exists:soutes,matricule_soute',
            'password' => 'nullable|string',
        ], [
            'email_or_matricule.required' => 'L\'email ou le matricule de l\'employé est requis.',
            'matricule_soute.required_without' => 'Le matricule de la soute est requis si une seule soute est associée.',
            'matricule_soute.exists' => 'Le matricule de soute est invalide.',
            'soute_id_selected.required_if' => 'Veuillez sélectionner une soute.',
            'soute_id_selected.exists' => 'La soute sélectionnée est invalide.',
        ]);

        $soute = null;
        if ($request->filled('soute_id_selected')) {
            $soute = Soute::find($request->soute_id_selected);
        } elseif ($request->filled('matricule_soute')) {
            $soute = Soute::where('matricule_soute', $request->matricule_soute)->first();
        }

        if (!$soute) {
            throw ValidationException::withMessages(['matricule_soute' => 'Soute non identifiée ou non sélectionnée.']);
        }

        $personnel = Personnel::where(function ($query) use ($request) {
            $query->where('email', $request->email_or_matricule)
                  ->orWhere('matricule', $request->email_or_matricule);
        })
        ->whereHas('soutes', function($q) use ($soute) {
            $q->where('soutes.id', $soute->id); // Utilisation du nom de table complet
        })
        ->first();

        if (!$personnel) {
            throw ValidationException::withMessages(['email_or_matricule' => 'Email/Matricule employé invalide ou non assigné à la soute sélectionnée.']);
        }

        if ($personnel->password === null) {
            Auth::guard('personnel_soute')->login($personnel);
            $request->session()->regenerate(); // Régénérer la session
            session(['active_soute_id' => $soute->id]); // Mettre l'ID de la soute en session
            return redirect()->route('soute.dashboard.set.password')
                             ->with('status', 'Bienvenue ! Veuillez définir votre mot de passe.');
        } else {
            if (empty($request->password)) {
                 throw ValidationException::withMessages(['password' => 'Le mot de passe est requis.']);
            }

            $loginField = filter_var($request->email_or_matricule, FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule';
            $credentials = [
                $loginField => $request->email_or_matricule,
                'password' => $request->password,
                // Tu pourrais ajouter une condition pour que le personnel soit lié à la soute ici aussi,
                // mais c'est déjà vérifié plus haut lors de la récupération de $personnel.
                // 'soute_id' => $soute->id, // Cela ne fonctionnerait pas directement avec attempt()
                                           // si soute_id n'est pas une colonne directe sur personnels.
            ];

            if (Auth::guard('personnel_soute')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                session(['active_soute_id' => $soute->id]); // Mettre l'ID de la soute en session
                return redirect()->intended(route('soute.dashboard.index'));
            }

            throw ValidationException::withMessages([
                $loginField => __('auth.failed'),
            ]);
        }
    }

    public function showSetPasswordForm()
    {
        if (!Auth::guard('personnel_soute')->check() || Auth::guard('personnel_soute')->user()->password !== null) {
            // Si l'utilisateur n'est pas "semi-loggué" ou a déjà un mdp, on le renvoie au login normal
            return redirect()->route('soute.dashboard.login');
        }
        return view('pompiste.auth.set_password');
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ],[
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $personnel = Auth::guard('personnel_soute')->user();
        $activeSouteId = session('active_soute_id'); // Essayer de récupérer la soute active

        if (!$activeSouteId && $personnel && $personnel->soutes()->count() > 0) {
            // Si la session a été perdue mais que le personnel est toujours "loggué" (via le middleware auth:personnel_soute)
            // et qu'il a des soutes, on pourrait tenter de prendre la première.
            // Cependant, le flux normal est que active_soute_id soit défini par login().
            // Si elle n'est pas là, c'est un souci.
            // Pour plus de sécurité, on pourrait forcer une reconnexion.
             Log::warning('Active_soute_id perdue pour le personnel ID: ' . $personnel->id . ' lors du setPassword.');
             Auth::guard('personnel_soute')->logout();
             $request->session()->invalidate();
             $request->session()->regenerateToken();
             return redirect()->route('soute.dashboard.login')->withErrors(['error' => 'Session de soute invalide. Veuillez vous reconnecter.']);
        }


        if ($personnel && $personnel->password === null && $activeSouteId) {
            $personnel->password = Hash::make($request->password);
            $personnel->save();

            // Re-logger l'utilisateur pour s'assurer que la session est fraîche et contient le bon état.
            Auth::guard('personnel_soute')->login($personnel, $request->filled('remember_me_after_set_password')); // 'remember'
            $request->session()->regenerate();
            session(['active_soute_id' => $activeSouteId]); // S'assurer qu'elle est bien remise après régénération

            return redirect()->route('soute.dashboard.index')->with('status', 'Mot de passe défini avec succès ! Vous êtes connecté.');
        }

        return redirect()->route('soute.dashboard.login')->withErrors(['email_or_matricule' => 'Impossible de définir le mot de passe ou session invalide.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('personnel_soute')->logout(); // Déconnecte l'utilisateur du guard 'personnel_soute'

        $request->session()->invalidate(); // Invalide la session actuelle

        $request->session()->regenerateToken(); // Régénère le token CSRF

        session()->forget('active_soute_id'); // Nettoyer la session spécifique à la soute

        // REDIRECTION EXPLICITE vers la page de login du Soute Dashboard
        return redirect()->route('soute.dashboard.login')
                         ->with('status', 'Vous avez été déconnecté avec succès de l\'espace soute.'); // Message optionnel
    }
}