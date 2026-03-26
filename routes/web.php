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
            return view('dashboard.registro.index');
        })->name('dashboard.entradas.registro.index');
    });

    Route::prefix('salidas')->group(function () {
        Route::get('/', function () {
            return view('dashboard.salidas');
        })->name('dashboard.salidas.index');
        Route::get('/registro', function () {
            return view('dashboard.registro.index');
        })->name('dashboard.salidas.registro.index');
    });
});