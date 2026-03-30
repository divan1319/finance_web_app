<?php

namespace App\Http\Controllers\Dashboard;

use App\Database\Entradas;
use App\Database\Salidas;
use App\Http\Controllers\Controller;
use App\Services\ActualizarGastoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD de lectura, actualización y borrado de entradas y salidas del usuario autenticado.
 *
 * Los ids de ruta se validan contra el propietario del registro; si no existe, responde 404.
 */
class GastoController extends Controller
{
    public function __construct(
        private ActualizarGastoService $actualizarGastoService,
    ) {}

    /**
     * Listado de todas las entradas del usuario.
     */
    public function indexEntradas(Request $request): View
    {
        $gastos = (new Entradas)->listar((int) $request->user()->id);

        return view('dashboard.entradas', compact('gastos'));
    }

    /**
     * Listado de todas las salidas del usuario.
     */
    public function indexSalidas(Request $request): View
    {
        $gastos = (new Salidas)->listar((int) $request->user()->id);

        return view('dashboard.salidas', compact('gastos'));
    }

    /**
     * Detalle de una entrada; aborta con 404 si no pertenece al usuario.
     */
    public function showEntrada(Request $request, int $gasto): View
    {
        $registro = (new Entradas)->buscarPorId($gasto, (int) $request->user()->id);
        abort_if($registro === null, 404);

        return view('dashboard.entradas.show', ['registro' => $registro]);
    }

    /**
     * Detalle de una salida; aborta con 404 si no pertenece al usuario.
     */
    public function showSalida(Request $request, int $gasto): View
    {
        $registro = (new Salidas)->buscarPorId($gasto, (int) $request->user()->id);
        abort_if($registro === null, 404);

        return view('dashboard.salidas.show', ['registro' => $registro]);
    }

    /**
     * Persiste cambios en una entrada vía {@see ActualizarGastoService::actualizarEntrada()}.
     */
    public function updateEntrada(Request $request, int $gasto): RedirectResponse
    {
        $this->actualizarGastoService->actualizarEntrada($request, $gasto);

        return redirect()->route('dashboard.entradas.show', $gasto)->with('ok', 'Registro actualizado correctamente.');
    }

    /**
     * Persiste cambios en una salida vía {@see ActualizarGastoService::actualizarSalida()}.
     */
    public function updateSalida(Request $request, int $gasto): RedirectResponse
    {
        $this->actualizarGastoService->actualizarSalida($request, $gasto);

        return redirect()->route('dashboard.salidas.show', $gasto)->with('ok', 'Registro actualizado correctamente.');
    }

    /**
     * Elimina una entrada del usuario y redirige al índice con mensaje flash.
     */
    public function destroyEntrada(Request $request, int $gasto): RedirectResponse
    {
        $ok = (new Entradas)->eliminar($gasto, (int) $request->user()->id);

        if (! $ok) {
            return redirect()->route('dashboard.entradas.index')->with('error', 'No se pudo eliminar el registro.');
        }

        return redirect()->route('dashboard.entradas.index')->with('ok', 'Registro eliminado correctamente.');
    }

    /**
     * Elimina una salida del usuario y redirige al índice con mensaje flash.
     */
    public function destroySalida(Request $request, int $gasto): RedirectResponse
    {
        $ok = (new Salidas)->eliminar($gasto, (int) $request->user()->id);

        if (! $ok) {
            return redirect()->route('dashboard.salidas.index')->with('error', 'No se pudo eliminar el registro.');
        }

        return redirect()->route('dashboard.salidas.index')->with('ok', 'Registro eliminado correctamente.');
    }
}
