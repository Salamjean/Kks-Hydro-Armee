<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CorpsArmeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Corps\ServiceController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('superadmin')->prefix('super/admin')->name('superadmin.')->group(function(){
    Route::get('dashboard',[SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('logout',[SuperAdminController::class, 'logout'])->name('logout');

    //Routes pour creer pour la gestion du SEA
    Route::get('create/SEA',[AdminController::class,'create'])->name('create.SEA');
    Route::post('/SEA/create',[AdminController::class,'store'])->name('store.SEA');
    Route::get('SEA/edit/{id}',[AdminController::class,'edit'])->name('edit.SEA');
    Route::put('SEA/update/{id}',[AdminController::class,'update'])->name('update.SEA');
    Route::delete('SEA/delete/{id}',[AdminController::class,'delete'])->name('delete.SEA');
});

Route::prefix('super-admin')->name('superadmin.')->group(function(){
    Route::get('register',[SuperAdminController::class,'register'])->name('register');
    Route::post('register',[SuperAdminController::class,'handleRegister'])->name('handleRegister');
    Route::get('login',[SuperAdminController::class,'login'])->name('login');
    Route::post('login',[SuperAdminController::class,'handleLogin'])->name('handleLogin');
});

Route::get('/validate-sea-account/{email}', [AdminController::class, 'defineAccess']);
Route::post('/validate-sea-account/{email}', [AdminController::class, 'submitDefineAccess'])->name('admin.validate');
Route::get('/validate-corps-account/{email}', [CorpsArmeController::class, 'defineAccess']);
Route::post('/validate-corps-account/{email}', [CorpsArmeController::class, 'submitDefineAccess'])->name('corps.validate');

//les routes de conneion admin 
Route::get('admin/login',[AdminController::class,'login'])->name('admin.login');
Route::post('admin/login',[AdminController::class,'handleLogin'])->name('admin.handleLogin');

//les routes de conneion admin 
Route::get('corps/login',[CorpsArmeController::class,'login'])->name('corps.login');
Route::post('corps/login',[CorpsArmeController::class,'handleLogin'])->name('corps.handleLogin');

// Routes Admin Connecté
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function(){ // Ajout du préfixe et nom ici pour simplifier
    Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard'); // URI: /admin/dashboard, Nom: admin.dashboard
    Route::post('/logout',[AdminController::class,'logout'])->name('logout'); // URI: /admin/logout, Nom: admin.logout (POST est mieux)

    // Routes pour la gestion des corps d'armée par l'admin
    Route::get('/create/army',[CorpsArmeController::class,'createArmy'])->name('create.army');
    Route::post('/store/army',[CorpsArmeController::class,'storeArmy'])->name('store.army');
    Route::get('/army',[CorpsArmeController::class,'index'])->name('army');
    Route::get('/army/edit/{id}',[CorpsArmeController::class,'editArmy'])->name('edit.army');
    Route::put('/army/update/{id}',[CorpsArmeController::class,'updateArmy'])->name('update.army');
    Route::delete('/army/delete/{id}',[CorpsArmeController::class,'deleteArmy'])->name('delete.army');
});

// Routes pour l'authentification et la gestion des Corps d'Armée (Utilisateurs)
Route::prefix('corps')->name('corps.')->group(function () { // Groupe principal pour TOUT ce qui concerne 'corps'

    // --- Routes Publiques (Connexion / Validation) ---
    Route::get('/login', [CorpsArmeController::class, 'login'])->name('login'); // URI: /corps/login, Nom: corps.login
    Route::post('/login', [CorpsArmeController::class, 'handleLogin'])->name('handle.login'); // URI: /corps/login, Nom: corps.handle.login
    Route::get('/define-access/{email}', [CorpsArmeController::class, 'defineAccess'])->name('define.access'); // URI: /corps/define-access/{email}, Nom: corps.define.access
    Route::post('/submit-define-access', [CorpsArmeController::class, 'submitDefineAccess'])->name('submit.define.access'); // URI: /corps/submit-define-access, Nom: corps.submit.define.access
    // La route /validate-corps-account/{email} est peut-être redondante avec define-access ? A vérifier.

    // --- Routes Protégées (nécessitent que l'utilisateur 'corps' soit connecté) ---
    Route::middleware('auth:corps')->group(function () {

        // Tableau de bord générique (si tu en as un)
        // Route::get('/dashboard', [CorpsArmeController::class, 'dashboard'])->name('dashboard'); // URI: /corps/dashboard, Nom: corps.dashboard

        // Routes spécifiques par corps d'armée (Tableaux de bord)
        Route::get('/gendarmerie/dashboard', function () {
            if (auth('corps')->user()->name !== 'Gendarmerie') { abort(403, 'Accès non autorisé.'); }
            return view('corpsArme.gendarmerie.dashboard');
        })->name('gendarmerie.dashboard'); // URI: /corps/gendarmerie/dashboard, Nom: corps.gendarmerie.dashboard

        Route::get('/marine/dashboard', function () {
            if (auth('corps')->user()->name !== 'Marine') { abort(403); }
            return view('corpsArme.marine.dashboard');
        })->name('marine.dashboard'); // URI: /corps/marine/dashboard, Nom: corps.marine.dashboard

        Route::get('/armee-air/dashboard', function () {
            if (auth('corps')->user()->name !== 'Armée-Air') { abort(403); }
            return view('corpsArme.armee-air.dashboard');
        })->name('armee-air.dashboard'); // URI: /corps/armee-air/dashboard, Nom: corps.armee-air.dashboard

        Route::get('/armee-terre/dashboard', function () {
            if (auth('corps')->user()->name !== 'Armée-Terre') { abort(403); }
            return view('corpsArme.armee-terre.dashboard');
        })->name('armee-terre.dashboard'); // URI: /corps/armee-terre/dashboard, Nom: corps.armee-terre.dashboard


        // --- Gestion des Services ---
        // **CETTE LIGNE EST MAINTENANT AU BON ENDROIT**
        Route::resource('services', ServiceController::class);
        // Elle va générer :
        // - URI: /corps/services, Nom: corps.services.index
        // - URI: /corps/services/create, Nom: corps.services.create
        // - etc.
        // Et toutes ces routes auront le middleware auth:corps

        // --- Routes pour les autres sections (à ajouter ICI plus tard) ---
        // Route::resource('personnel', PersonnelController::class);
        // Route::resource('distributeurs', DistributeurController::class);
        // Route::resource('carburant', CarburantController::class);


        // Route de déconnexion
        Route::post('/logout', [CorpsArmeController::class, 'logout'])->name('logout'); // URI: /corps/logout, Nom: corps.logout (POST est mieux)

    }); // Fin du groupe middleware('auth:corps')

}); // Fin du groupe prefix('corps')->name('corps.')


// Supprime ce bloc redondant si dashboard et logout sont déjà dans le groupe principal protégé
Route::middleware('auth:corps')->group(function(){
    Route::get('corps/dashboard',[CorpsArmeController::class,'dashboard'])->name('corps.dashboard');
 Route::get('corps/logout',[CorpsArmeController::class,'logout'])->name('corps.logout');
});
 