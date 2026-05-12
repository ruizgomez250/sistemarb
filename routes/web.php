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

    // ========== SOCIOS ==========
    Route::prefix('socios')->name('socios.')->group(function () {
        Route::get('/', [SocioController::class, 'index'])->name('index');
        Route::get('/create', [SocioController::class, 'create'])->name('create');
        Route::post('/', [SocioController::class, 'store'])->name('store');
        Route::get('/{socio}', [SocioController::class, 'show'])->name('show');
        Route::get('/{socio}/edit', [SocioController::class, 'edit'])->name('edit');
        Route::put('/{socio}', [SocioController::class, 'update'])->name('update');
        Route::delete('/{socio}', [SocioController::class, 'destroy'])->name('destroy');
        Route::get('/buscar/{cedula}', [SocioController::class, 'getByCedula'])->name('buscar');
        Route::get('/search/ajax', [SocioController::class, 'search'])->name('search');
        Route::get('/export/excel', [SocioController::class, 'exportExcel'])->name('export.excel');
    });

    // ========== COPAVIC ==========
    Route::prefix('copavic')->name('copavic.')->group(function () {
        Route::get('/', [CopavicController::class, 'index'])->name('index');
        Route::get('/dashboard', [CopavicController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [CopavicController::class, 'create'])->name('create');
        Route::post('/', [CopavicController::class, 'store'])->name('store');
        Route::get('/{copavic}', [CopavicController::class, 'show'])->name('show');
        Route::get('/{copavic}/edit', [CopavicController::class, 'edit'])->name('edit');
        Route::put('/{copavic}', [CopavicController::class, 'update'])->name('update');
        Route::delete('/{copavic}', [CopavicController::class, 'destroy'])->name('destroy');
        Route::get('/buscar/cedula/{cedula}', [CopavicController::class, 'getByCedula'])->name('buscar.cedula');
        Route::get('/buscar/socio/{socio}', [CopavicController::class, 'getBySocio'])->name('buscar.socio');
        Route::get('/search/ajax', [CopavicController::class, 'search'])->name('search');
        Route::get('/export/excel', [CopavicController::class, 'exportExcel'])->name('export.excel');
    });

    // ========== COOPERATIVA UNIVERSITARIA ==========
    Route::prefix('coop-universitaria')->name('coop-universitaria.')->group(function () {
        Route::get('/', [CoopUniversitariaController::class, 'index'])->name('index');
        Route::get('/dashboard', [CoopUniversitariaController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [CoopUniversitariaController::class, 'create'])->name('create');
        Route::post('/', [CoopUniversitariaController::class, 'store'])->name('store');
        Route::get('/{coopUniversitaria}', [CoopUniversitariaController::class, 'show'])->name('show');
        Route::get('/{coopUniversitaria}/edit', [CoopUniversitariaController::class, 'edit'])->name('edit');
        Route::put('/{coopUniversitaria}', [CoopUniversitariaController::class, 'update'])->name('update');
        Route::delete('/{coopUniversitaria}', [CoopUniversitariaController::class, 'destroy'])->name('destroy');
        Route::get('/buscar/cedula/{cedula}', [CoopUniversitariaController::class, 'getByCedula'])->name('buscar.cedula');
        Route::get('/buscar/socio/{socio}', [CoopUniversitariaController::class, 'getBySocio'])->name('buscar.socio');
        Route::get('/search/ajax', [CoopUniversitariaController::class, 'search'])->name('search');
        Route::get('/export/excel', [CoopUniversitariaController::class, 'exportExcel'])->name('export.excel');
    });

    // ========== PADRÓN ILUMINADO ==========
    Route::prefix('padron-iluminado')->name('padron-iluminado.')->group(function () {
        Route::get('/', [PadronIluminadoController::class, 'index'])->name('index');
        Route::get('/dashboard', [PadronIluminadoController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [PadronIluminadoController::class, 'create'])->name('create');
        Route::post('/', [PadronIluminadoController::class, 'store'])->name('store');
        Route::get('/{padronIluminado}', [PadronIluminadoController::class, 'show'])->name('show');
        Route::get('/{padronIluminado}/edit', [PadronIluminadoController::class, 'edit'])->name('edit');
        Route::put('/{padronIluminado}', [PadronIluminadoController::class, 'update'])->name('update');
        Route::delete('/{padronIluminado}', [PadronIluminadoController::class, 'destroy'])->name('destroy');
        Route::get('/buscar/cedula/{cedula}', [PadronIluminadoController::class, 'getByCedula'])->name('buscar.cedula');
        Route::get('/search/ajax', [PadronIluminadoController::class, 'search'])->name('search');
        Route::get('/export/excel', [PadronIluminadoController::class, 'exportExcel'])->name('export.excel');
    });

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