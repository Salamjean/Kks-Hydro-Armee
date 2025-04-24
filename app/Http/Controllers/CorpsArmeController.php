<?php

namespace App\Http\Controllers;

use App\Models\CorpsArme;
use App\Models\ResetCodePasswordCorpsArme;
use App\Notifications\SendEmailToCorpsArmeAfterRegistrationNotification;
use Exception;
use Illuminate\Http\Request;
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
        //Vérification si le sous-admin existe déjà
        $checkSousadminExiste = CorpsArme::where('email', $email)->first();
        if($checkSousadminExiste){
            return view('corpsArme.auth.validate', compact('email'));
        }else{
            return redirect()->route('corpsArme.auth.login')->with('error', 'Email inconnu');
        };
    }

    public function submitDefineAccess(Request $request)
    {
        // Validation des données
        $request->validate([
            'code' => 'required|exists:reset_code_password_corps_armes,code',
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
            $corps = CorpsArme::where('email', $request->email)->first();
    
            if ($corps) {
                // Mise à jour du mot de passe
                $corps->password = Hash::make($request->password);
    
                // Traitement de l'image de profil
                if ($request->hasFile('profile_picture')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($corps->profile_picture) {
                        Storage::delete('public/' . $corps->profile_picture); // Assurez-vous du 'public/' ici
                    }
    
                    // Stocker la nouvelle image
                    $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $corps->profile_picture = $imagePath;
                }
    
                $corps->update();
    
                if ($corps) {
                    $existingcodecorps = ResetCodePasswordCorpsArme::where('email', $corps->email)->count();
    
                    if ($existingcodecorps > 1) {
                        ResetCodePasswordCorpsArme::where('email', $corps->email)->delete();
                    }
                }
    
                return redirect()->route('corps.login')->with('success', 'Accès definir avec succès');
            } else {
                return redirect()->back()->with('error', 'Email inconnu');
            }
        } catch (\Exception $e) {
            Log::error('Error updating admin profile: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage())->withInput();
        }
    }

    public function logout(){
        auth('corps')->logout();
        return redirect()->route('corps.login')->with('success', 'Vous êtes déconnecté avec succès');
    }
}


