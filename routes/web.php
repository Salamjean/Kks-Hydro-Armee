<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CorpsArmeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Corps\ServiceController;
use App\Http\Controllers\Corps\PersonnelController;
use App\Http\Controllers\Corps\DistributeurController;
use App\Http\Controllers\Corps\CarburantController;
use App\Http\Controllers\Corps\SouteController; 
use App\Http\Controllers\Soute\SoutePersonnelLoginController;
use App\Http\Controllers\Soute\SouteDashboardController;
use App\Http\Controllers\CorpsDashboards\GendarmerieController;
use App\Http\Controllers\CorpsDashboards\MarineController;
use App\Http\Controllers\CorpsDashboards\ArmeeAirController;
use App\Http\Controllers\Pompiste\PompisteController;
use App\Http\Controllers\CorpsDashboards\ArmeeTerreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return view('auth.choose_login'); 
})->name('home');

Route::prefix('super-admin')->name('superadmin.')->group(function(){
    Route::get('register',[SuperAdminController::class,'register'])->name('register');
    Route::post('register',[SuperAdminController::class,'handleRegister'])->name('handleRegister');
    Route::get('login',[SuperAdminController::class,'login'])->name('login');
    Route::post('login',[SuperAdminController::class,'handleLogin'])->name('handleLogin');

    Route::middleware('superadmin')->group(function(){ 
        Route::get('dashboard',[SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout',[SuperAdminController::class, 'logout'])->name('logout');

        Route::get('create/SEA',[AdminController::class,'create'])->name('create.SEA');
        Route::post('/SEA/create',[AdminController::class,'store'])->name('store.SEA');
        Route::get('SEA/edit/{id}',[AdminController::class,'edit'])->name('edit.SEA');
        Route::put('SEA/update/{id}',[AdminController::class,'update'])->name('update.SEA');
        Route::delete('SEA/delete/{id}',[AdminController::class,'delete'])->name('delete.SEA');
    });
});

Route::get('/validate-sea-account/{email}', [AdminController::class, 'defineAccess']); 
Route::post('/validate-sea-account', [AdminController::class, 'submitDefineAccess'])->name('admin.validate');
Route::get('/validate-corps-account/{email}', [CorpsArmeController::class, 'defineAccess']);
Route::post('/validate-corps-account', [CorpsArmeController::class, 'submitDefineAccess'])->name('corps.submit.define.access');

Route::prefix('admin')->name('admin.')->group(function() {
    Route::get('/login',[AdminController::class,'login'])->name('login');
    Route::post('/login',[AdminController::class,'handleLogin'])->name('handleLogin');

    Route::middleware('auth:admin')->group(function(){
        Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
        Route::post('/logout',[AdminController::class,'logout'])->name('logout');

        Route::get('/army/create',[CorpsArmeController::class,'createArmy'])->name('create.army');
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
        Route::get('/gendarmerie/dashboard', [GendarmerieController::class, 'index'])->name('gendarmerie.dashboard');
        Route::get('/marine/dashboard', [MarineController::class, 'index'])->name('marine.dashboard');
        Route::get('/armee-air/dashboard', [ArmeeAirController::class, 'index'])->name('armee-air.dashboard');
        Route::get('/armee-terre/dashboard', [ArmeeTerreController::class, 'index'])->name('armee-terre.dashboard');

        Route::resource('services', ServiceController::class)->except(['create', 'show']);
        Route::resource('personnel', PersonnelController::class)->except(['create','show']);
        Route::get('/personnel/{personnel}/assigner-soutes', [PersonnelController::class, 'showAssignSoutesForm'])->name('personnel.assignSoutesForm');
        Route::post('/personnel/{personnel}/assigner-soutes', [PersonnelController::class, 'handleAssignSoutes'])->name('personnel.handleAssignSoutes');
        Route::resource('distributeurs', DistributeurController::class)->except(['create','show']);
        Route::resource('carburants', CarburantController::class)->except(['create', 'show']);

        Route::get('personnel/chauffeur-armee-terre', [PersonnelController::class, 'chauffeur_armee_terre'])->name('armee_terre.personnel.chauffeur');
        Route::get('personnel/chauffeur-gendarmerie', [PersonnelController::class, 'chauffeur_gendarmerie'])->name('gendarmerie.personnel.chauffeur');
        Route::get('personnel/chauffeur-armee-air', [PersonnelController::class, 'chauffeur_armee_air'])->name('armee_air.personnel.chauffeur');
        Route::get('personnel/chauffeur-marine', [PersonnelController::class, 'chauffeur_marine'])->name('marine.personnel.chauffeur');
        Route::get('personnel/{id}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
        Route::put('personnel/{id}', [PersonnelController::class, 'update'])->name('personnel.update');
        Route::delete('personnel/{id}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');
        Route::resource('soutes', SouteController::class)->except(['show']);
        Route::post('/logout', [CorpsArmeController::class, 'logout'])->name('logout');

    });

});

Route::prefix('soute-dashboard')->name('soute.dashboard.')->group(function() {
    // Routes pour les utilisateurs non connectés (ou en cours de première connexion)
    Route::middleware('guest:personnel_soute')->group(function() {
        Route::get('/login', [SoutePersonnelLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SoutePersonnelLoginController::class, 'login'])->name('handleLogin');
        
    });

    Route::get('services/distribution', [PompisteController::class, 'distribution'])->name('services.distribution');
    
    Route::get('services/depotage', [PompisteController::class, 'depotage'])->name('services.depotage');
    Route::get('rapport', [PompisteController::class, 'rapport'])->name('rapport');
    Route::get('rapports', [PompisteController::class, 'rapport'])->name('rapports.index');
    Route::get('/rapoport/index', [PompisteController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('/statistiques/export/pdf', [PompisteController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/statistiques/export/excel', [PompisteController::class, 'exportExcel'])->name('export.excel');

  // LA ROUTE POUR ENREGISTRER LA DISTRIBUTION
  Route::post('/distributions', [PompisteController::class, 'storeDistribution'])->name('pompiste.store.distribution');
  // LA ROUTE POUR ENREGISTRER LE DEPOTAGE
  Route::post('/depotages', [PompisteController::class, 'storeDepotage'])->name('pompiste.store.depotage'); // Correction du nom ici

    // Route AJAX pour récupérer les informations du personnel et de ses soutes
    Route::post('/get-personnel-soute-info', [SoutePersonnelLoginController::class, 'getPersonnelSouteInfo'])
           ->name('getPersonnelSouteInfo');

    // Routes pour définir le mot de passe (l'utilisateur doit être "semi-authentifié" par le login initial)
    Route::middleware('auth:personnel_soute')->group(function() {
        Route::get('/set-password', [SoutePersonnelLoginController::class, 'showSetPasswordForm'])->name('set.password');
        Route::post('/set-password', [SoutePersonnelLoginController::class, 'setPassword'])->name('handleSet.password');
      
    });

    // Routes du Dashboard Soute (nécessite une authentification complète et un mot de passe défini)
    Route::middleware(['auth:personnel_soute', 'hasSoutePasswordSet'])->group(function() {
        Route::get('/', [SouteDashboardController::class, 'index'])->name('index');
        Route::post('/logout', [SoutePersonnelLoginController::class, 'logout'])->name('logout'); 
       
    });

 Route::middleware('auth:corps')->group(function(){
    Route::get('corps/dashboard',[CorpsArmeController::class,'dashboard'])->name('corps.dashboard');
    Route::get('corps/logout',[CorpsArmeController::class,'logout'])->name('corps.logout');
});

});
