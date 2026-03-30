<?php

namespace App\Services;

use App\Database\Entradas;
use App\Database\GestorFacturas;
use App\Database\Salidas;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Orquesta la actualización de un movimiento (entrada o salida) del usuario autenticado.
 *
 * Valida nombre, monto, fecha y factura opcional; comprueba que el registro exista y pertenezca
 * al usuario; si hay nueva factura, la guarda con {@see GestorFacturas}, actualiza la fila y
 * elimina la factura anterior solo si la persistencia tuvo éxito. Los fallos de negocio o de
 * archivo se exponen como {@see ValidationException}; ausencia de registro como 404.
 */
class ActualizarGastoService
{
    public function __construct(
        private GestorFacturas $gestorFacturas,
    ) {}

    /**
     * Actualiza una entrada (ingreso) identificada por su id de registro.
     *
     * @throws ValidationException Si la validación falla o no se puede completar la actualización.
     */
    public function actualizarEntrada(Request $request, int $gastoId): void
    {
        $this->actualizar($request, new Entradas, $gastoId);
    }

    /**
     * Actualiza una salida (egreso) identificada por su id de registro.
     *
     * @throws ValidationException Si la validación falla o no se puede completar la actualización.
     */
    public function actualizarSalida(Request $request, int $gastoId): void
    {
        $this->actualizar($request, new Salidas, $gastoId);
    }

    /**
     * Flujo común de actualización sobre el repositorio dado.
     *
     * @param  Entradas|Salidas  $repo  Tabla de entradas o salidas.
     *
     * @throws ValidationException Errores de validación o de guardado de factura.
     */
    private function actualizar(Request $request, Entradas|Salidas $repo, int $gastoId): void
    {
        $validados = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
            'factura' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $userId = (int) $request->user()->id;
        $actual = $repo->buscarPorId($gastoId, $userId);

        if ($actual === null) {
            abort(404);
        }

        $nombre = $validados['nombre'];
        $monto = (float) $validados['monto'];
        $fecha = $validados['fecha'];

        try {
            if ($request->hasFile('factura')) {
                $archivo = $request->file('factura');
                if (! $archivo instanceof UploadedFile) {
                    throw ValidationException::withMessages(['factura' => 'No se recibió un archivo válido.']);
                }
                $nuevaUrl = $this->gestorFacturas->guardar($this->arrayDesdeUploadedFile($archivo));
                $ok = $repo->actualizar($gastoId, $userId, $nombre, $monto, $fecha, $nuevaUrl);
                if ($ok) {
                    $this->gestorFacturas->eliminar($actual['factura_url']);
                } else {
                    $this->gestorFacturas->eliminar($nuevaUrl);
                    throw ValidationException::withMessages(['nombre' => 'No se pudo actualizar el registro.']);
                }
            } else {
                $ok = $repo->actualizar($gastoId, $userId, $nombre, $monto, $fecha, $actual['factura_url']);
                if (! $ok) {
                    throw ValidationException::withMessages(['nombre' => 'No se pudo actualizar el registro.']);
                }
            }
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['factura' => $e->getMessage()]);
        }
    }

    /**
     * Convierte un archivo subido al arreglo que espera {@see GestorFacturas::guardar()}.
     *
     * @return array{name: string, type: string, tmp_name: string, error: int, size: int}
     *
     * @throws RuntimeException Si no se puede resolver la ruta temporal del archivo.
     */
    private function arrayDesdeUploadedFile(UploadedFile $archivo): array
    {
        $ruta = $archivo->getRealPath();
        if ($ruta === false) {
            throw new RuntimeException('No se pudo leer el archivo subido.');
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
