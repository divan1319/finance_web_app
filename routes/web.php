<?php

use Illuminate\Support\Facades\Route;
// Importamos el controlador que creamos en la carpeta Controllers
use App\Http\Controllers\Login; 
use App\Http\Controllers\GastoController;

Route::middleware(['auth'])->group(function () {
    Route::post('/gastos/guardar', [GastoController::class, 'store'])->name('gastos.store');
});

// 1. RUTAS DE INICIO Y LOGIN
Route::get('/', function () {
    return view('welcome');
});

// Esta ruta muestra el formulario
Route::get('/login', function () {
    return view('login');
})->name('login');

// ESTA ES LA LÍNEA NUEVA: Recibe los datos cuando das clic en "Iniciar sesión"
Route::post('/login', [Login::class, 'loguear']);


// 2. RUTAS DEL DASHBOARD (PROTEGIDAS)
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    
    // Cambiamos el 'return view' por el controlador
    Route::get('/', [GastoController::class, 'index'])->name('dashboard.index');

    Route::prefix('entradas')->group(function () {
        Route::get('/', function () { return view('dashboard.entradas'); })->name('dashboard.entradas.index');
        // También aquí si quieres ver la tabla en registro
        Route::get('/registro', [GastoController::class, 'index'])->name('dashboard.entradas.registro.index');
    });

    Route::prefix('salidas')->group(function () {
        Route::get('/', function () { return view('dashboard.salidas'); })->name('dashboard.salidas.index');
        // Y aquí
        Route::get('/registro', [GastoController::class, 'index'])->name('dashboard.salidas.registro.index');
    });

    Route::delete('/gastos/{gasto}', [App\Http\Controllers\GastoController::class, 'destroy'])->name('gastos.destroy');
});