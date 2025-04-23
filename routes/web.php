<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CorpsArmeController;
use App\Http\Controllers\SuperAdminController;
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

Route::middleware('auth:admin')->group(function(){
    Route::get('admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
    Route::get('admin/logout',[AdminController::class,'logout'])->name('admin.logout');

    // Routes pour la creation des coprs d'armée 
    Route::get('admin/create/army',[CorpsArmeController::class,'createArmy'])->name('admin.create.army');
    Route::post('admin/store/army',[CorpsArmeController::class,'storeArmy'])->name('admin.store.army');
    Route::get('admin/army',[CorpsArmeController::class,'index'])->name('admin.army');
    Route::get('admin/army/edit/{id}',[CorpsArmeController::class,'editArmy'])->name('admin.edit.army');
    Route::put('admin/army/update/{id}',[CorpsArmeController::class,'updateArmy'])->name('admin.update.army');
    Route::delete('admin/army/delete/{id}',[CorpsArmeController::class,'deleteArmy'])->name('admin.delete.army');
});

Route::middleware('auth:corps')->group(function(){
    Route::get('corps/dashboard',[CorpsArmeController::class,'dashboard'])->name('corps.dashboard');
    Route::get('corps/logout',[CorpsArmeController::class,'logout'])->name('corps.logout');
});
