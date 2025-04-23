<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard(){
        return view('superadmin.dashboard');
    }

    public function register(){
        return view('superadmin.auth.register');
    }

    public function handleRegister(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:super_admins,email',
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required|same:password',
        ],[
            'email.required' => 'l\'adresse mail est obligatoire',
            'email.email' => 'l\'adresse doit être de type email',
            'email.unique' => 'cet adresse mail existe déjà',
            'password.required' => 'le mot de passe est obligatoire',
            'password.same' => 'les mots de passes ne sont pas identiques',
            'confirm_password.required' => 'vous devez confirmer le mot de passe',
            'confirm_password.same' => 'les mots de passes ne sont pas identiques',
        ]);
        try {
            $superadmin = new SuperAdmin();
            $superadmin->name = $request->name;
            $superadmin->email = $request->email;
            $superadmin->password = Hash::make($request->password);
            $superadmin->save();

            return redirect()->route('superadmin.dashboard')->with('sucess','Votre compte a été créer avec succès');
        } catch (Exception $e) {
           dd($e);
        }
    }

    public function login(){
        return view('superadmin.auth.login');
    }

    public function handleLogin(Request $request){
        $request->validate([
            'email' => 'required|email|exists:super_admins,email',
            'password' => 'required'
        ],[
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);
        try {
           if(auth('superadmin')->attempt($request->only('email','password'))){
                return redirect()->route('superadmin.dashboard')->with('sucess','Heureux de vous revoir');
           } else{
            return redirect()->back()->with('error','mot de passe incorrect');
           }
        } catch (Exception $e) {
           dd($e);
        }
    }

    public function logout(){
        Auth::guard('superadmin')->logout();
        return redirect()->route('superadmin.login');
    }
}
