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
 * Registra nuevos movimientos financieros (entradas o salidas) con factura obligatoria.
 *
 * Valida los campos del formulario, persiste la imagen mediante {@see GestorFacturas} e inserta
 * la fila en la tabla correspondiente. Los errores de archivo o de guardado se traducen a
 * {@see ValidationException} para mostrarlos en el formulario.
 */
class RegistroGastoService
{
    public function __construct(
        private GestorFacturas $gestorFacturas,
    ) {}

    /**
     * Crea una nueva entrada (ingreso) para el usuario autenticado.
     *
     * @throws ValidationException Si falta la factura, no es válida o falla el almacenamiento.
     */
    public function almacenarEntrada(Request $request): void
    {
        $this->almacenar($request, function (int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void {
            $this->registrarEntrada($userId, $nombre, $monto, $fecha, $factura);
        });
    }

    /**
     * Crea una nueva salida (egreso) para el usuario autenticado.
     *
     * @throws ValidationException Si falta la factura, no es válida o falla el almacenamiento.
     */
    public function almacenarSalida(Request $request): void
    {
        $this->almacenar($request, function (int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void {
            $this->registrarSalida($userId, $nombre, $monto, $fecha, $factura);
        });
    }

    /**
     * Valida la petición y delega en el callback con user id y datos tipados.
     *
     * @param  callable(int, string, float, string, UploadedFile): void  $registrar  Inserta en entradas o salidas.
     *
     * @throws ValidationException Validación HTTP o mensaje derivado de {@see RuntimeException}.
     */
    private function almacenar(Request $request, callable $registrar): void
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
            $registrar(
                (int) $request->user()->id,
                $validados['nombre'],
                (float) $validados['monto'],
                $validados['fecha'],
                $archivo
            );
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['factura' => $e->getMessage()]);
        }
    }

    /**
     * Guarda la factura en disco y crea la fila en {@see Entradas}.
     *
     * @throws RuntimeException Propagado desde el guardado de archivo si aplica.
     */
    private function registrarEntrada(int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void
    {
        $facturaUrl = $this->gestorFacturas->guardar($this->arrayDesdeUploadedFile($factura));
        (new Entradas)->crearConFacturaUrl($userId, $nombre, $monto, $fecha, $facturaUrl);
    }

    /**
     * Guarda la factura en disco y crea la fila en {@see Salidas}.
     *
     * @throws RuntimeException Propagado desde el guardado de archivo si aplica.
     */
    private function registrarSalida(int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void
    {
        $facturaUrl = $this->gestorFacturas->guardar($this->arrayDesdeUploadedFile($factura));
        (new Salidas)->crearConFacturaUrl($userId, $nombre, $monto, $fecha, $facturaUrl);
    }

    /**
     * Formato compatible con {@see GestorFacturas::guardar()}.
     *
     * @return array{name: string, type: string, tmp_name: string, error: int, size: int}
     *
     * @throws RuntimeException Si no se puede leer la ruta temporal del archivo subido.
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
