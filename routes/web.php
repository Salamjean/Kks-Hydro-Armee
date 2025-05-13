<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CorpsArmeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Corps\ServiceController;
use App\Http\Controllers\Corps\PersonnelController;
use App\Http\Controllers\Corps\DistributeurController;
use App\Http\Controllers\Corps\CarburantController;
use App\Http\Controllers\Corps\SouteController; 
use App\Http\Controllers\Soute\SoutePersonnelLoginController; // <<--- AJOUTE CET IMPORT
use App\Http\Controllers\Soute\SouteDashboardController; // <<--- AJOUTE CET IMPORT (pour plus tard)
use App\Http\Controllers\CorpsDashboards\GendarmerieController;
use App\Http\Controllers\CorpsDashboards\MarineController;
use App\Http\Controllers\CorpsDashboards\ArmeeAirController;
use App\Http\Controllers\CorpsDashboards\ArmeeTerreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect; // Ajouté

// MODIFICATION : Route racine redirigeant vers la connexion Corps d'Armée
Route::get('/', function () {
    return view('auth.choose_login'); // Affiche la nouvelle vue
})->name('home');

// --- SUPER ADMIN ---
Route::prefix('super-admin')->name('superadmin.')->group(function(){
    // Routes publiques Super Admin (login, register)
    Route::get('register',[SuperAdminController::class,'register'])->name('register');
    Route::post('register',[SuperAdminController::class,'handleRegister'])->name('handleRegister');
    Route::get('login',[SuperAdminController::class,'login'])->name('login');
    Route::post('login',[SuperAdminController::class,'handleLogin'])->name('handleLogin');

    // Routes protégées Super Admin
    Route::middleware('superadmin')->group(function(){ // Le middleware est appliqué ici
        Route::get('dashboard',[SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout',[SuperAdminController::class, 'logout'])->name('logout'); // POST pour logout

        // Gestion du SEA par Super Admin
        Route::get('create/SEA',[AdminController::class,'create'])->name('create.SEA');
        Route::post('/SEA/create',[AdminController::class,'store'])->name('store.SEA');
        Route::get('SEA/edit/{id}',[AdminController::class,'edit'])->name('edit.SEA');
        Route::put('SEA/update/{id}',[AdminController::class,'update'])->name('update.SEA');
        Route::delete('SEA/delete/{id}',[AdminController::class,'delete'])->name('delete.SEA');
    });
});

// --- VALIDATION DE COMPTES (routes publiques accessibles via email) ---
Route::get('/validate-sea-account/{email}', [AdminController::class, 'defineAccess'])->name('admin.define.access'); // Nom plus explicite
Route::post('/validate-sea-account', [AdminController::class, 'submitDefineAccess'])->name('admin.submit.define.access'); // Pas besoin de {email} dans POST si l'email est dans le formulaire
Route::get('/validate-corps-account/{email}', [CorpsArmeController::class, 'defineAccess'])->name('corps.define.access'); // Nom plus explicite
Route::post('/validate-corps-account', [CorpsArmeController::class, 'submitDefineAccess'])->name('corps.submit.define.access'); // Pas besoin de {email}

// --- ADMIN (SEA) ---
Route::prefix('admin')->name('admin.')->group(function() {
    // Routes publiques Admin (login)
    Route::get('/login',[AdminController::class,'login'])->name('login');
    Route::post('/login',[AdminController::class,'handleLogin'])->name('handleLogin');

    Route::middleware('auth:admin')->group(function(){
        Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
        Route::post('/logout',[AdminController::class,'logout'])->name('logout'); // POST pour logout

        Route::get('/army/create',[CorpsArmeController::class,'createArmy'])->name('create.army'); // URI plus simple
        Route::post('/army',[CorpsArmeController::class,'storeArmy'])->name('store.army');
        Route::get('/army',[CorpsArmeController::class,'index'])->name('army');
        Route::get('/army/{id}/edit',[CorpsArmeController::class,'editArmy'])->name('edit.army');
        Route::put('/army/{id}',[CorpsArmeController::class,'updateArmy'])->name('update.army');
        Route::delete('/army/{id}',[CorpsArmeController::class,'deleteArmy'])->name('delete.army');
    });
});


Route::prefix('corps')->name('corps.')->group(function () {
    Route::get('/login', [CorpsArmeController::class, 'login'])->name('login');
    Route::post('/login', [CorpsArmeController::class, 'handleLogin'])->name('handle.login');

    Route::middleware('auth:corps')->group(function () {
        Route::get('/gendarmerie/dashboard', [GendarmerieDashboardController::class, 'index'])->name('gendarmerie.dashboard');

        Route::get('/marine/dashboard', [MarineDashboardController::class, 'index'])->name('marine.dashboard');

        Route::get('/armee-air/dashboard', [ArmeeAirDashboardController::class, 'index'])->name('armee-air.dashboard');

        Route::get('/armee-terre/dashboard', [ArmeeTerreDashboardController::class, 'index'])->name('armee-terre.dashboard');

        Route::resource('services', ServiceController::class)->except(['create', 'show']);
        Route::resource('personnel', PersonnelController::class)->except(['create','show']);
        Route::resource('distributeurs', DistributeurController::class)->except(['create','show']);
        Route::resource('carburants', CarburantController::class)->except(['create', 'show']);

Route::resource('soutes', SouteController::class)->except(['show']);
        Route::post('/logout', [CorpsArmeController::class, 'logout'])->name('logout');

    });

}); // Fin du groupe prefix('corps')->name('corps.')

Route::prefix('soute-dashboard')->name('soute.dashboard.')->group(function() {
   // Appliquer le middleware guest:personnel_soute ici
   Route::middleware('guest:personnel_soute')->group(function() {
    Route::get('/login', [SoutePersonnelLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SoutePersonnelLoginController::class, 'login'])->name('handleLogin');
    
});
Route::post('/get-personnel-soute-info', [SoutePersonnelLoginController::class, 'getPersonnelSouteInfo'])
       ->name('getPersonnelSouteInfo');
    // Page de définition du mot de passe (pour la première connexion)
    Route::get('/set-password', [SoutePersonnelLoginController::class, 'showSetPasswordForm'])->name('set.password')->middleware('auth:personnel_soute'); // Doit être connecté avec mdp null
    Route::post('/set-password', [SoutePersonnelLoginController::class, 'setPassword'])->name('handleSet.password')->middleware('auth:personnel_soute');

   // Routes protégées du dashboard soute
   Route::middleware(['auth:personnel_soute', 'hasSoutePasswordSet'])->group(function() {
    Route::get('/', [SouteDashboardController::class, 'index'])->name('index'); // Pointe vers SouteDashboardController@index
    Route::post('/logout', [SoutePersonnelLoginController::class, 'logout'])->name('logout');
});
      // --- Routes Protégées du Dashboard Soute (nécessitent mot de passe défini) ---
      Route::middleware(['auth:personnel_soute', 'hasSoutePasswordSet'])->group(function() {
        // La route du dashboard principal pour la soute
        Route::get('/', [SouteDashboardController::class, 'index'])->name('index');
        // ... ajoute ici d'autres routes spécifiques au dashboard de la soute ...
    });
});

// Le bloc suivant est redondant si les routes dashboard et logout sont bien dans le groupe protégé ci-dessus
 Route::middleware('auth:corps')->group(function(){
    Route::get('corps/dashboard',[CorpsArmeController::class,'dashboard'])->name('corps.dashboard');
    Route::get('corps/logout',[CorpsArmeController::class,'logout'])->name('corps.logout');
});
