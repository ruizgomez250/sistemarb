<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\ProfesionController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\CopavicController;
use App\Http\Controllers\CoopUniversitariaController;
use App\Http\Controllers\PadronIluminadoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\PunteroController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

// Ruta principal
Route::get('/home', [DashboardController::class, 'index'])
    ->name('home')
    ->middleware('auth');

Route::get('/', function () {
    return redirect('/home');
});

// Grupo de rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
   
    // ========== DASHBOARD ==========
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

    // ========== REGISTROS ==========
    Route::prefix('registros')->name('registros.')->group(function () {
        Route::get('/', [RegistroController::class, 'index'])->name('index');
        Route::get('/create', [RegistroController::class, 'create'])->name('create');
        Route::post('/', [RegistroController::class, 'store'])->name('store');
        Route::get('/{registro}', [RegistroController::class, 'show'])->name('show');
        Route::get('/{registro}/edit', [RegistroController::class, 'edit'])->name('edit');
        Route::put('/{registro}', [RegistroController::class, 'update'])->name('update');
        Route::delete('/{registro}', [RegistroController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [RegistroController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [RegistroController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/buscar/{cedula}', [RegistroController::class, 'buscarPorCedula'])->name('buscar');
        Route::get('/{registro}/print', [RegistroController::class, 'print'])->name('print');
    });

    // ========== BÚSQUEDA GENERAL PARA REGISTROS ==========
    Route::get('/buscar-persona/{cedula}', [RegistroController::class, 'buscarPersonaPorCedula'])->name('buscar.persona');

    // ========== MOTIVOS ==========
    Route::prefix('motivos')->name('motivos.')->group(function () {
        Route::get('/', [MotivoController::class, 'index'])->name('index');
        Route::post('/', [MotivoController::class, 'store'])->name('store');
        Route::get('/{motivo}', [MotivoController::class, 'show'])->name('show');
        Route::put('/{motivo}', [MotivoController::class, 'update'])->name('update');
        Route::delete('/{motivo}', [MotivoController::class, 'destroy'])->name('destroy');
        Route::get('/api/all', [MotivoController::class, 'getMotivosApi'])->name('api');
        Route::get('/export/excel', [MotivoController::class, 'exportExcel'])->name('export.excel');
        Route::get('/search', [MotivoController::class, 'search'])->name('search');
    });

    // ========== PROFESIONES ==========
    Route::prefix('profesiones')->name('profesiones.')->group(function () {
        Route::get('/', [ProfesionController::class, 'index'])->name('index');
        Route::get('/create', [ProfesionController::class, 'create'])->name('create');
        Route::post('/', [ProfesionController::class, 'store'])->name('store');
        Route::get('/{profesion}', [ProfesionController::class, 'show'])->name('show');
        Route::get('/{profesion}/edit', [ProfesionController::class, 'edit'])->name('edit');
        Route::put('/{profesion}', [ProfesionController::class, 'update'])->name('update');
        Route::delete('/{profesion}', [ProfesionController::class, 'destroy'])->name('destroy');
        Route::get('/api/all', [ProfesionController::class, 'getProfesionesApi'])->name('api');
    });

    

    // ========== COPAVIC ==========
    

    // ========== COOPERATIVA UNIVERSITARIA ==========
    

    // ========== PADRÓN ILUMINADO ==========
    

    // ========== TUS RUTAS EXISTENTES ==========
    Route::resource('useradmin', UserAdminController::class);
    Route::post('sistema', [SistemaController::class, 'store'])->name('sistema.store');
    Route::delete('sistema/{id}', [SistemaController::class, 'destroy'])->name('sistema.destroy');
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profiles', [ProfilesController::class, 'index'])->name('profiles');
    Route::resource('users', UserController::class);
    Route::resource('roles', RolesController::class);
    Route::get('roles/{role}/give-permissions', [RolesController::class, 'addPermissionToRole'])->name('roles.addpermissionrole');
    Route::put('roles/{role}/give-permissions', [RolesController::class, 'givePermissionToRole'])->name('roles.updatepermissionrole');
    Route::resource('permissions', PermissionController::class);
    
    Route::get('/buscar-personas-padron', [PunteroController::class, 'buscarPersonas'])
        ->name('buscar.personas.padron');
    
    Route::get('/sistemas/{sistema}/punteros', [PunteroController::class, 'porSistema'])->name('sistemas.punteros');
});

// Ruta de prueba para verificar que las rutas funcionan
Route::get('/test', function () {
    return response()->json(['message' => 'Las rutas funcionan correctamente']);
});