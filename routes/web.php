<?php

use Illuminate\Support\Facades\Route;
use App\Database\ReporteBalance;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reporte', function () {
    $reporte = new ReporteBalance();
    $userId = 1; // Aquí deberías usar el ID del usuario autenticado, p.ej. auth()->id()
    
    $resumen = $reporte->obtenerResumen($userId);
    $historial = $reporte->obtenerHistorialCombinado($userId);
    
    return view('reporte', compact('resumen', 'historial'));
});
