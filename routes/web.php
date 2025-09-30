<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RecoleccionController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Rutas Web de EcoGestión
|--------------------------------------------------------------------------
| Define la navegación principal de la aplicación.
| - '/' (welcome): pantalla de selección de rol previa al login.
| - '/dashboard': panel principal (requiere autenticación).
| - recursos: solicitudes y recolecciones (protegidos por 'auth').
*/

//
// Pantalla inicial con selección de rol (no requiere login)
//
Route::view('/', 'welcome')->name('access');

//
// Rutas protegidas por autenticación
//
Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Exportar solicitudes a CSV (se declara antes que el resource)
    Route::get('solicitudes/export', [SolicitudController::class, 'export'])
        ->name('solicitudes.export');

    // Recurso RESTful para Solicitudes
    Route::resource('solicitudes', SolicitudController::class)
        ->parameters(['solicitudes' => 'solicitud'])
        ->whereNumber('solicitud');

    // Recurso parcial para Recolecciones (sólo index/store/update)
    Route::resource('recolecciones', RecoleccionController::class)
        ->only(['index','store','update']);

    // Rutas de Perfil (Breeze/Jetstream)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//
// Carga de rutas de autenticación (login, registro, etc.)
//
require __DIR__.'/auth.php';