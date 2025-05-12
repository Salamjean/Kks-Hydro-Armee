<?php

namespace App\Http\Controllers;

use App\Models\CorpsArme;
use App\Models\ResetCodePasswordCorpsArme;
use App\Notifications\SendEmailToCorpsArmeAfterRegistrationNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Important pour l'authentification
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CorpsArmeController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }
    public function createArmy()
    {
        return view('corpsArme.create');
    }

    public function storeArmy(Request $request){
        $request->validate([
            'name' => 'required|unique:corps_armes,name',
            'email' => 'required|email|unique:corps_armes,email',
            'localisation' => 'required',
        ],[
            'name.required' => 'Le corps armée est obligatoire',
            'name.unique' => 'Cet corps d\'armée est déjà enregistrer',
            'email.required' => 'Le mail est obligatoire',
            'email.email' => 'Le mail doit être de type mail',
            'email.unique' => 'Cet mail à déjà été attribuer à un autre corps d\'armée',
            'localisation.required' => 'La localisation est obligatoire',
        ]);
        try {
            $corpsArme = new CorpsArme();
            $corpsArme->name = $request->name;
            $corpsArme->localisation = $request->localisation;
            $corpsArme->email = $request->email;
            $corpsArme->password = Hash::make('default');
            $corpsArme->save();

            // Envoi de l'e-mail de vérification
                ResetCodePasswordCorpsArme::where('email', $corpsArme->email)->delete();
                $code = rand(10000, 40000);
                ResetCodePasswordCorpsArme::create([
                    'code' => $code,
                    'email' => $corpsArme->email,
                ]);

            Notification::route('mail', $corpsArme->email)
             ->notify(new SendEmailToCorpsArmeAfterRegistrationNotification($code, $corpsArme->email));

            return redirect()->route('admin.army')->with('success','Corps armée ajouter avec succès');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function index()
    {
        $corpsArmes = CorpsArme::all();
        return view('corpsArme.index', compact('corpsArmes'));
    }

    public function editArmy($id) {
        // Récupérer le corps d'armée par son ID
        $corpsArme = CorpsArme::findOrFail($id);
        return view('corpsArme.edit', compact('corpsArme'));
    }

    public function updateArmy(Request $request, $id){
        $request->validate([
            'name' => 'required|unique:corps_armes,name,'.$id,
            'email' => 'required|email|unique:corps_armes,email,'.$id,
            'localisation' => 'required',
        ],[
            'name.required' => 'Le nom est obligatoire',
            'name.unique' => 'Cet corps d\'armée est déjà enregistrer',
            'email.required' => 'Le mail est obligatoire',
            'email.email' => 'Le mail doit être de type mail',
            'email.unique' => 'Cet mail à déjà été attribuer à un autre corps d\'armée',
            'localisation.required' => 'La localisation est obligatoire',
        ]);
        try {
            $corpsArme = CorpsArme::find($id);
            $corpsArme->name = $request->name;
            $corpsArme->localisation = $request->localisation;
            $corpsArme->email = $request->email;
            $corpsArme->save();

            return redirect()->route('admin.army')->with('success','Corps armée modifier avec succès');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function deleteArmy($id){
        $corpsArme = CorpsArme::findOrFail($id);
        $corpsArme->delete();
        return redirect()->back()->with('success','Corps armée supprimer avec succès');
    }

    public function defineAccess($email){
        // Vérification si le sous-admin existe déjà
        $checkCorpsExiste = CorpsArme::where('email', $email)->first();
        if($checkCorpsExiste){
            // Vérifie si un mot de passe autre que 'default' est déjà défini
            // Si oui, on ne devrait peut-être pas permettre de redéfinir facilement ?
            // Ou alors, c'est la procédure de "mot de passe oublié" qui utilise ce flux ?
            // Pour l'instant, on assume que c'est pour la première définition.
            return view('corpsArme.auth.validate', compact('email'));
        }else{
            // Redirige vers la page de login principale ou une page d'erreur générale
             return redirect()->route('welcome')->with('error', 'Email inconnu ou accès non autorisé.');
             // Ou si vous avez une page de login générique avant de choisir le type d'utilisateur :
             // return redirect()->route('login')->with('error', 'Email inconnu.');
        };
    }

    public function submitDefineAccess(Request $request)
    {
        // Validation des données
        $request->validate([
            // Assurez-vous que la validation du code vérifie aussi l'email associé si nécessaire
            'code' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = ResetCodePasswordCorpsArme::where('email', $request->email)
                                                        ->where('code', $value)
                                                        ->exists();
                    if (!$exists) {
                        $fail('Le code fourni est invalide ou a expiré pour cet email.');
                    }
                },
            ],
            'password' => 'required|min:8|same:confirme_password', // Ajout de min:8 pour la sécurité
            'confirme_password' => 'required|same:password',
            'email' => 'required|email|exists:corps_armes,email' // Valide que l'email existe
        ], [
            'code.required' => 'Le code de validation est obligatoire. Veuillez vérifier votre email.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.required' => 'Le mot de passe de confirmation est obligatoire.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Format d\'email invalide.',
            'email.exists' => 'Aucun compte trouvé pour cet email.',
        ]);

        try {
            $corps = CorpsArme::where('email', $request->email)->first();

            // Double vérification (même si validé par 'exists' et le check de code)
            if (!$corps) {
                 return redirect()->back()->with('error', 'Utilisateur non trouvé.')->withInput();
            }

            // Mise à jour du mot de passe
            $corps->password = Hash::make($request->password);

            // Optionnel: Traitement de l'image de profil (si vous l'ajoutez au formulaire)
            // if ($request->hasFile('profile_picture')) {
            //     // ... (logique de stockage d'image) ...
            //     // $corps->profile_picture = $imagePath;
            // }

            $corps->save(); // Utiliser save() au lieu de update() si on modifie plusieurs attributs

            // Suppression du code utilisé
            // On supprime TOUS les codes pour cet email pour éviter les conflits
            ResetCodePasswordCorpsArme::where('email', $corps->email)->delete();

            // *** Redirection vers la page de connexion comme demandé ***
            return redirect()->route('corps.login')->with('success', 'Votre mot de passe a été défini avec succès. Vous pouvez maintenant vous connecter.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la définition de l\'accès CorpsArme: ' . $e->getMessage());
            return back()->with('error', 'Une erreur technique est survenue. Veuillez réessayer.')->withInput();
        }
    }

    // Vue de connexion pour les Corps d'Armée
    public function login(){
        // S'assurer que le guard est bien 'corps' par défaut si nécessaire
        // ou spécifier le guard lors de la tentative de connexion
        return view('corpsArme.auth.login');
    }

    // Gestion de la soumission du formulaire de connexion
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:corps_armes,email',
            'password' => 'required', // Ne pas mettre de min ici, juste vérifier si présent
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.exists' => 'Aucun compte trouvé avec cette adresse email.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = $request->only('email', 'password');

        // Tenter la connexion avec le guard 'corps'
        if (Auth::guard('corps')->attempt($credentials, $request->filled('remember'))) {
            // Authentification réussie
            $request->session()->regenerate();

            $user = Auth::guard('corps')->user();
            $corpsName = strtolower($user->name); // ex: "gendarmerie", "marine", etc.

            // Redirection dynamique basée sur le nom du corps
            switch ($corpsName) {
                case 'gendarmerie':
                    // Assurez-vous que cette route existe dans web.php
                    return redirect()->intended(route('corps.gendarmerie.dashboard'));
                case 'marine':
                    return redirect()->intended(route('corps.marine.dashboard'));
                case 'armée-air': // Attention au nom exact utilisé dans la base
                    return redirect()->intended(route('corps.armee-air.dashboard'));
                case 'armée-terre': // Attention au nom exact
                    return redirect()->intended(route('corps.armee-terre.dashboard'));
                default:
                    // Redirection générique si le nom ne correspond à aucun cas spécifique
                    Log::warning("Corps d'armée non reconnu pour la redirection : " . $user->name);
                 
                     return redirect()->intended(route('corps.dashboard')); // Assurez-vous que cette route existe
            }

        }

        // Échec de l'authentification
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email'); // Renvoyer seulement l'email, pas le mot de passe
    }


    // Tableau de bord générique (ou à adapter)
    // public function dashboard()
    // {
    //     // Cette méthode pourrait ne pas être utilisée si vous redirigez directement
    //     // vers des dashboards spécifiques dans handleLogin.
    //     // Sinon, elle pourrait afficher une vue commune ou rediriger à nouveau.

    //     $user = Auth::guard('corps')->user();
    //     // Exemple simple: afficher une vue générique en passant le nom du corps
    //     return view('corpsArme.dashboard', ['corpsName' => $user->name]);

    //     // Ou vous pouvez refaire la logique de redirection ici si nécessaire
    //     // $corpsName = strtolower($user->name);
    //     // switch ($corpsName) { ... }
    // }


    public function logout(Request $request){ // Ajouter Request ici
        Auth::guard('corps')->logout();

        $request->session()->invalidate(); // Invalider la session
        $request->session()->regenerateToken(); // Régénérer le token CSRF

        return redirect()->route('corps.login')->with('success', 'Vous êtes déconnecté avec succès');
    }
}