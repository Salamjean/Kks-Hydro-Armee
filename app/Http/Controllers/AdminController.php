<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ResetCodePasswordAdmin;
use App\Notifications\SendEmailToAdminAfterRegistrationNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }
    public function create(){
        $admins = Admin::all();
        
        return view('admin.create', compact('admins'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'matricule' => 'required|unique:admins,matricule',
        ],[
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'Le mail est obligatoire',
            'email.email' => 'Le mail doit être de type mail',
            'email.unique' => 'Cet adresse mail existe déjà',
            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Le matricule existe déjà',
        ]);
        try {
            $admin = new Admin();
            $admin->name = $request->name;
            $admin->matricule = $request->matricule;
            $admin->email = $request->email;
            $admin->password = Hash::make('default');
            $admin->save();

            // Envoi de l'e-mail de vérification
                ResetCodePasswordAdmin::where('email', $admin->email)->delete();
                $code = rand(10000, 40000);
                ResetCodePasswordAdmin::create([
                    'code' => $code,
                    'email' => $admin->email,
                ]);

            Notification::route('mail', $admin->email)
             ->notify(new SendEmailToAdminAfterRegistrationNotification($code, $admin->email));

            return redirect()->back()->with('success','SEA ajouter avec succès');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function edit($id){
        $admin = Admin::findOrFail($id); // Utilisez findOrFail pour gérer les IDs inexistants
        return view('admin.edit', compact('admin'));
    }
    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id,
            'matricule' => 'required|unique:admins,matricule,'.$id,
        ],[
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'Le mail est obligatoire',
            'email.email' => 'Le mail doit être de type mail',
            'email.unique' => 'Cet adresse mail existe déjà',
            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Le matricule existe déjà',
        ]);
        try {
            $admin = Admin::find($id);
            $admin->name = $request->name;
            $admin->matricule = $request->matricule;
            $admin->email = $request->email;
            $admin->save();

            return redirect()->route('superadmin.create.SEA')->with('success','SEA modifier avec succès');
        } catch (Exception $e) {
            dd($e);
        }
    }
    public function delete($id){
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect()->back()->with('success','SEA supprimer avec succès');
    }

    public function defineAccess($email){
        //Vérification si le sous-admin existe déjà
        $checkSousadminExiste = Admin::where('email', $email)->first();
        if($checkSousadminExiste){
            return view('admin.auth.validate', compact('email'));
        }else{
            return redirect()->route('admin.auth.login')->with('error', 'Email inconnu');
        };
    }

    public function submitDefineAccess(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'code' => 'required|exists:reset_code_password_admins,code',
        'password' => 'required|same:confirme_password',
        'confirme_password' => 'required|same:password',
    ], [
        'code.exists' => 'Le code de réinitialisation est invalide.',
        'code.required' => 'Le code de réinitialisation est obligatoire. Veuillez vérifier votre email.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.same' => 'Les mots de passe doivent être identiques.',
        'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        'confirme_password.required' => 'Le mot de passe de confirmation est obligatoire.',
    ]);

    try {
        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            // Mise à jour du mot de passe
            $admin->password = Hash::make($request->password);

            // Traitement de l'image de profil
            if ($request->hasFile('profile_picture')) {
                // Supprimer l'ancienne photo si elle existe
                if ($admin->profile_picture) {
                    Storage::delete('public/' . $admin->profile_picture); // Assurez-vous du 'public/' ici
                }

                // Stocker la nouvelle image
                $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                $admin->profile_picture = $imagePath;
            }

            $admin->update();

            if ($admin) {
                $existingcodeadmin = ResetCodePasswordAdmin::where('email', $admin->email)->count();

                if ($existingcodeadmin > 1) {
                    ResetCodePasswordAdmin::where('email', $admin->email)->delete();
                }
            }

            return redirect()->route('admin.login')->with('success', 'Compte mis à jour avec succès');
        } else {
            return redirect()->route('admin.login')->with('error', 'Email inconnu');
        }
    } catch (\Exception $e) {
        Log::error('Error updating admin profile: ' . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage())->withInput();
    }
}

public function login(){
    return view('admin.auth.login');
 }

 public function handleLogin(Request $request)
 {
     // Validation des champs du formulaire
     $request->validate([
         'email' => 'required|exists:admins,email',
         'password' => 'required|min:8',
     ], [
         'email.required' => 'Le mail est obligatoire.',
         'email.exists' => 'Cette adresse mail n\'existe pas.',
         'password.required' => 'Le mot de passe est obligatoire.',
         'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
     ]);
 
     try {
        if(auth('admin')->attempt($request->only('email', 'password')))
        {
            return redirect()->route('admin.dashboard')->with('Bienvenu sur votre page ');
        }else{
            return redirect()->back()->with('error', 'Votre mot de passe ou votre adresse mail est incorrect.');
        }
    } catch (Exception $e) {
        dd($e);
    }
 }

    public function logout(){
        auth('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Vous êtes déconnecté avec succès');
    }
}
