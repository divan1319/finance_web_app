<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Dashboard\GastoController;
use App\Http\Controllers\Dashboard\RegistroGastoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::prefix('entradas')->group(function () {
        Route::get('/', [GastoController::class, 'indexEntradas'])->name('dashboard.entradas.index');
        Route::get('/registro', function () {
            return view('dashboard.registro.entrada');
        })->name('dashboard.entradas.registro.entrada');
        Route::post('/registro', [RegistroGastoController::class, 'storeEntrada'])
            ->name('dashboard.entradas.registro.store');
        Route::get('/{gasto}', [GastoController::class, 'showEntrada'])
            ->whereNumber('gasto')
            ->name('dashboard.entradas.show');
        Route::put('/{gasto}', [GastoController::class, 'updateEntrada'])
            ->whereNumber('gasto')
            ->name('dashboard.entradas.update');
        Route::delete('/{gasto}', [GastoController::class, 'destroyEntrada'])
            ->whereNumber('gasto')
            ->name('dashboard.entradas.destroy');
    });

    Route::prefix('salidas')->group(function () {
        Route::get('/', [GastoController::class, 'indexSalidas'])->name('dashboard.salidas.index');
        Route::get('/registro', function () {
            return view('dashboard.registro.salida');
        })->name('dashboard.salidas.registro.salida');
        Route::post('/registro', [RegistroGastoController::class, 'storeSalida'])
            ->name('dashboard.salidas.registro.store');
        Route::get('/{gasto}', [GastoController::class, 'showSalida'])
            ->whereNumber('gasto')
            ->name('dashboard.salidas.show');
        Route::put('/{gasto}', [GastoController::class, 'updateSalida'])
            ->whereNumber('gasto')
            ->name('dashboard.salidas.update');
        Route::delete('/{gasto}', [GastoController::class, 'destroySalida'])
            ->whereNumber('gasto')
            ->name('dashboard.salidas.destroy');
    });
});
