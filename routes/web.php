<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::prefix('entradas')->group(function () {
        Route::get('/', function () {
            return view('dashboard.entradas');
        })->name('dashboard.entradas.index');
        Route::get('/registro', function () {
            return view('dashboard.registro.entrada');
        })->name('dashboard.entradas.registro.entrada');
    });

    Route::prefix('salidas')->group(function () {
        Route::get('/', function () {
            return view('dashboard.salidas');
        })->name('dashboard.salidas.index');
        Route::get('/registro', function () {
            return view('dashboard.registro.salida');
        })->name('dashboard.salidas.registro.salida');
    });
});