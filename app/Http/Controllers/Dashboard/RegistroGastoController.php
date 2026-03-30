<?php

namespace App\Http\Controllers\Dashboard;

use App\Database\Entradas;
use App\Database\GestorFacturas;
use App\Database\Salidas;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class RegistroGastoController extends Controller
{
    public function storeEntrada(Request $request): RedirectResponse
    {
        return $this->almacenar($request, new Entradas, 'dashboard.entradas.index', 'Entrada registrada correctamente.');
    }

    public function storeSalida(Request $request): RedirectResponse
    {
        return $this->almacenar($request, new Salidas, 'dashboard.salidas.index', 'Salida registrada correctamente.');
    }

    private function almacenar(Request $request, Entradas|Salidas $repo, string $rutaExito, string $mensaje): RedirectResponse
    {
        $validados = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
            'factura' => ['required', 'file', 'image', 'max:5120'],
        ]);

        $archivo = $request->file('factura');
        if (! $archivo instanceof UploadedFile) {
            throw ValidationException::withMessages(['factura' => 'No se recibió un archivo válido.']);
        }

        try {
            $repo->crear(
                (int) $request->user()->id,
                $validados['nombre'],
                (float) $validados['monto'],
                $validados['fecha'],
                $this->comoArrayArchivoSubido($archivo)
            );
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['factura' => $e->getMessage()]);
        }

        return redirect()->route($rutaExito)->with('ok', $mensaje);
    }

    /**
     * Formato compatible con {@see GestorFacturas::guardar()}.
     *
     * @return array{name: string, type: string, tmp_name: string, error: int, size: int}
     */
    private function comoArrayArchivoSubido(UploadedFile $archivo): array
    {
        $ruta = $archivo->getRealPath();
        if ($ruta === false) {
            throw ValidationException::withMessages(['factura' => 'No se pudo leer el archivo subido.']);
        }

        return [
            'name' => $archivo->getClientOriginalName(),
            'type' => $archivo->getMimeType() ?? 'application/octet-stream',
            'tmp_name' => $ruta,
            'error' => $archivo->getError(),
            'size' => (int) $archivo->getSize(),
        ];
    }
}
