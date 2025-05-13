<?php

namespace App\Http\Controllers\Soute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Personnel;
use App\Models\Soute; // Si tu dois vérifier le matricule_soute
use Illuminate\Validation\ValidationException; // Pour les erreurs de login

class SoutePersonnelLoginController extends Controller
{
    /**
     * Constructeur pour appliquer le middleware guest aux méthodes de login/set_password
     * sauf pour logout et les méthodes de définition de mot de passe qui nécessitent une "semi-auth".
     */
    // public function __construct()
    // {
    //     // Applique le middleware 'guest' au guard 'personnel_soute'
    //     // pour les méthodes showLoginForm et login,
    //     // sauf si on est déjà en train de définir le mot de passe.
    //     $this->middleware('guest:personnel_soute')->except(['logout', 'showSetPasswordForm', 'setPassword']);
    // }

    /**
     * Affiche le formulaire de connexion pour le personnel de soute.
     */
    public function showLoginForm()
    {
        return view('pompiste.auth.login'); // Vue à créer
    }
    public function getPersonnelSouteInfo(Request $request)
    {
        $request->validate(['email_or_matricule' => 'required|string']);

        $personnel = Personnel::where(function ($query) use ($request) {
                            $query->where('email', $request->email_or_matricule)
                                  ->orWhere('matricule', $request->email_or_matricule);
                        })
                        // CHARGER LA RELATION 'soutes' (Many-to-Many)
                        ->with('soutes')
                        ->first();

        if ($personnel) {
            // Logique pour la relation Many-to-Many
            if ($personnel->soutes && $personnel->soutes->count() > 0) {
                if ($personnel->soutes->count() === 1) {
                    // Si une seule soute, on la présélectionne
                    $soute = $personnel->soutes->first();
                    return response()->json([
                        'success' => true,
                        'soutes' => [['id' => $soute->id, 'matricule_soute' => $soute->matricule_soute, 'nom' => $soute->nom]],
                        'multiple_soutes' => false, // Indique au JS qu'il n'y a pas besoin de select
                    ]);
                } else {
                    // Si plusieurs soutes, on renvoie la liste pour que l'utilisateur choisisse
                    $soutesData = $personnel->soutes->map(function ($soute) {
                        return ['id' => $soute->id, 'matricule_soute' => $soute->matricule_soute, 'nom' => $soute->nom];
                    });
                    return response()->json([
                        'success' => true,
                        'soutes' => $soutesData,
                        'multiple_soutes' => true, // Indique au JS d'afficher le select
                    ]);
                }
            }
            // Si aucune soute n'est assignée (même avec la relation Many-to-Many)
            return response()->json(['success' => false, 'message' => 'Aucune soute n\'est assignée à ce personnel.']);
        }
        return response()->json(['success' => false, 'message' => 'Personnel non trouvé.']);
    }
    /**
     * Gère la tentative de connexion du personnel de soute.
     */
    public function login(Request $request)
    {
        $request->validate([
          'email_or_matricule' => 'required|string',
            // S'il y a une sélection de soute, l'ID de la soute sera envoyé.
            'soute_id_selected' => 'required_if:multiple_soutes_found,true|nullable|integer|exists:soutes,id',
            // Le matricule_soute est toujours requis pour identifier la soute cible (si pas de sélection)
            'matricule_soute' => 'required_without:soute_id_selected|nullable|string|exists:soutes,matricule_soute',
            'password' => 'nullable|string',
        ], [
            'email_or_matricule.required' => 'L\'email ou le matricule de l\'employé est requis.',
            'matricule_soute.required_without' => 'Le matricule de la soute est requis.',
            'matricule_soute.exists' => 'Le matricule de soute est invalide.',
            'soute_id_selected.required_if' => 'Veuillez sélectionner une soute.',
            'soute_id_selected.exists' => 'La soute sélectionnée est invalide.',
        ]);

        // 1. Trouver la soute par son matricule
        $soute = null;
        if ($request->filled('soute_id_selected')) {
            $soute = Soute::find($request->soute_id_selected);
        } elseif($request->filled('matricule_soute')) {
            $soute = Soute::where('matricule_soute', $request->matricule_soute)->first();
        }

        if (!$soute) {
            throw ValidationException::withMessages(['matricule_soute' => 'Soute non identifiée.']);
        }

        $personnel = Personnel::where(function ($query) use ($request) {
            $query->where('email', $request->email_or_matricule)
                  ->orWhere('matricule', $request->email_or_matricule);
        })
        // VÉRIFICATION POUR MANY-TO-MANY
        ->whereHas('soutes', function($q) use ($soute) {
           $q->where('soutes.id', $soute->id); // S'assurer que le personnel est lié à la soute sélectionnée/identifiée
        })
        ->first();

        if (!$personnel) {
            throw ValidationException::withMessages(['email_or_matricule' => 'Email/Matricule employé invalide ou non assigné à la soute sélectionnée.']);
        }

        // 3. Vérifier le mot de passe
        if ($personnel->password === null) {
            // Première connexion, pas de mot de passe défini
            // Authentifier temporairement pour permettre la définition du mot de passe
            Auth::guard('personnel_soute')->login($personnel);
            return redirect()->route('soute.dashboard.set.password')
                             ->with('status', 'Bienvenue ! Veuillez définir votre mot de passe.');
        } else {
            // L'utilisateur a déjà un mot de passe, tenter une connexion normale
            if (empty($request->password)) {
                 throw ValidationException::withMessages(['password' => 'Le mot de passe est requis.']);
            }

            $credentials = [
                // Utilise le champ qui est unique pour la connexion (email ou matricule)
                // Si tu permets les deux, il faut choisir lequel est prioritaire pour 'attempt'
                // Ou s'assurer que l'un des deux est l'identifiant principal pour 'attempt'
                // Ici, on va supposer que 'email' est le champ principal pour 'attempt' si disponible
                // ou 'matricule' sinon. Mais 'attempt' est plus simple si on utilise un seul champ.
                // Pour simplifier, si 'email' est le champ d'identification principal pour Auth::attempt
                // il faudrait que l'utilisateur entre son email.
                // Pour cet exemple, on va prendre le champ qui a été trouvé :
                (filter_var($request->email_or_matricule, FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule') => $request->email_or_matricule,
                'password' => $request->password,
                // On peut ajouter une condition pour s'assurer que le personnel est actif si tu as un tel champ
                // 'is_active' => 1,
            ];


            if (Auth::guard('personnel_soute')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('soute.dashboard.index'));
            }

            throw ValidationException::withMessages([
                // Utilise le nom du champ principal pour l'erreur
                (filter_var($request->email_or_matricule, FILTER_VALIDATE_EMAIL) ? 'email_or_matricule' : 'email_or_matricule') => __('auth.failed'),
            ]);
        }
    }

    /**
     * Affiche le formulaire pour définir le mot de passe (première connexion).
     */
    public function showSetPasswordForm()
    {
        // L'utilisateur doit être authentifié via le guard 'personnel_soute'
        // et son mot de passe doit être null (géré par le middleware HasSoutePasswordSet aussi)
        if (Auth::guard('personnel_soute')->user()->password !== null) {
            return redirect()->route('soute.dashboard.index'); // Déjà un mot de passe, redirige
        }
        return view('pompiste.auth.set_password'); // Vue à créer
    }

    /**
     * Enregistre le nouveau mot de passe.
     */
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
        if ($personnel && $personnel->password === null) {
            $personnel->password = Hash::make($request->password);
            $personnel->save();

            // Optionnel: re-logger l'utilisateur pour rafraîchir la session avec le mdp
            // Auth::guard('personnel_soute')->login($personnel, true);

            return redirect()->route('soute.dashboard.index')->with('status', 'Mot de passe défini avec succès ! Vous êtes connecté.');
        }

        return redirect()->route('soute.dashboard.login')->withErrors(['email_or_matricule' => 'Impossible de définir le mot de passe.']);
    }

    /**
     * Déconnecte le personnel de soute.
     */
    public function logout(Request $request)
    {
        Auth::guard('personnel_soute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('soute.dashboard.login');
    }
}