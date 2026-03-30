<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\RegistroGastoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Punto HTTP para crear nuevas entradas o salidas (POST) delegando en {@see RegistroGastoService}.
 */
class RegistroGastoController extends Controller
{
    public function __construct(
        private RegistroGastoService $registroGastoService,
    ) {}

    /**
     * Registra una entrada y redirige al listado de entradas con mensaje de éxito.
     */
    public function storeEntrada(Request $request): RedirectResponse
    {
        $this->registroGastoService->almacenarEntrada($request);

        return redirect()->route('dashboard.entradas.index')->with('ok', 'Entrada registrada correctamente.');
    }

    /**
     * Registra una salida y redirige al listado de salidas con mensaje de éxito.
     */
    public function storeSalida(Request $request): RedirectResponse
    {
        $this->registroGastoService->almacenarSalida($request);

        return redirect()->route('dashboard.salidas.index')->with('ok', 'Salida registrada correctamente.');
    }
}
