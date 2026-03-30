<?php

namespace App\Http\Controllers\Dashboard;

use App\Database\Entradas;
use App\Database\Salidas;
use App\Http\Controllers\Controller;
use App\Services\ActualizarGastoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GastoController extends Controller
{
    public function __construct(
        private ActualizarGastoService $actualizarGastoService,
    ) {}

    public function indexEntradas(Request $request): View
    {
        $gastos = (new Entradas)->listar((int) $request->user()->id);

        return view('dashboard.entradas', compact('gastos'));
    }

    public function indexSalidas(Request $request): View
    {
        $gastos = (new Salidas)->listar((int) $request->user()->id);

        return view('dashboard.salidas', compact('gastos'));
    }

    public function showEntrada(Request $request, int $gasto): View
    {
        $registro = (new Entradas)->buscarPorId($gasto, (int) $request->user()->id);
        abort_if($registro === null, 404);

        return view('dashboard.entradas.show', ['registro' => $registro]);
    }

    public function showSalida(Request $request, int $gasto): View
    {
        $registro = (new Salidas)->buscarPorId($gasto, (int) $request->user()->id);
        abort_if($registro === null, 404);

        return view('dashboard.salidas.show', ['registro' => $registro]);
    }

    public function updateEntrada(Request $request, int $gasto): RedirectResponse
    {
        $this->actualizarGastoService->actualizarEntrada($request, $gasto);

        return redirect()->route('dashboard.entradas.show', $gasto)->with('ok', 'Registro actualizado correctamente.');
    }

    public function updateSalida(Request $request, int $gasto): RedirectResponse
    {
        $this->actualizarGastoService->actualizarSalida($request, $gasto);

        return redirect()->route('dashboard.salidas.show', $gasto)->with('ok', 'Registro actualizado correctamente.');
    }

    public function destroyEntrada(Request $request, int $gasto): RedirectResponse
    {
        $ok = (new Entradas)->eliminar($gasto, (int) $request->user()->id);

        if (! $ok) {
            return redirect()->route('dashboard.entradas.index')->with('error', 'No se pudo eliminar el registro.');
        }

        return redirect()->route('dashboard.entradas.index')->with('ok', 'Registro eliminado correctamente.');
    }

    public function destroySalida(Request $request, int $gasto): RedirectResponse
    {
        $ok = (new Salidas)->eliminar($gasto, (int) $request->user()->id);

        if (! $ok) {
            return redirect()->route('dashboard.salidas.index')->with('error', 'No se pudo eliminar el registro.');
        }

        return redirect()->route('dashboard.salidas.index')->with('ok', 'Registro eliminado correctamente.');
    }
}
