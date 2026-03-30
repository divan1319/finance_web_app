<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\RegistroGastoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegistroGastoController extends Controller
{
    public function __construct(
        private RegistroGastoService $registroGastoService,
    ) {}

    public function storeEntrada(Request $request): RedirectResponse
    {
        $this->registroGastoService->almacenarEntrada($request);

        return redirect()->route('dashboard.entradas.index')->with('ok', 'Entrada registrada correctamente.');
    }

    public function storeSalida(Request $request): RedirectResponse
    {
        $this->registroGastoService->almacenarSalida($request);

        return redirect()->route('dashboard.salidas.index')->with('ok', 'Salida registrada correctamente.');
    }
}
