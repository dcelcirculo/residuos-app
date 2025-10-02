<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RecoleccionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportsController;
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
    Route::get('recolecciones/export', [RecoleccionController::class, 'export'])
        ->name('recolecciones.export');
    Route::resource('recolecciones', RecoleccionController::class)
        ->only(['index','store','update']);

    // Rutas de Perfil (Breeze/Jetstream)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Solo admin
    Route::middleware('can:admin-only')->group(function () {
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

        // Gestión puntual de usuarios (buscar / actualizar / eliminar)
        Route::get('/admin/users/manage', [AdminController::class, 'manageUsers'])->name('admin.users.manage');
        Route::post('/admin/users/search', [AdminController::class, 'searchUser'])->name('admin.users.search');
        Route::post('/admin/users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

        Route::post('/admin/settings/points', [AdminController::class, 'updatePointsFormula'])->name('admin.settings.points');

        // Reportes para administradores
        Route::get('/admin/reports/usuarios', [ReportsController::class, 'userReport'])->name('reports.user');
        Route::get('/admin/reports/global', [ReportsController::class, 'usersSummary'])->name('reports.users');
        Route::get('/admin/reports/empresas', [ReportsController::class, 'companyReport'])->name('reports.company');

        Route::patch('/admin/recolecciones/{recoleccion}', [AdminController::class, 'updateRecoleccion'])->name('admin.recolecciones.update');
    });
});

//
// Carga de rutas de autenticación (login, registro, etc.)
//
require __DIR__.'/auth.php';
