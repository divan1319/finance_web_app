<?php

namespace App\Services;

use App\Database\Entradas;
use App\Database\GestorFacturas;
use App\Database\Salidas;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class RegistroGastoService
{
    public function __construct(
        private GestorFacturas $gestorFacturas,
    ) {}

    public function almacenarEntrada(Request $request): void
    {
        $this->almacenar($request, function (int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void {
            $this->registrarEntrada($userId, $nombre, $monto, $fecha, $factura);
        });
    }

    public function almacenarSalida(Request $request): void
    {
        $this->almacenar($request, function (int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void {
            $this->registrarSalida($userId, $nombre, $monto, $fecha, $factura);
        });
    }

    /**
     * @param  callable(int, string, float, string, UploadedFile): void  $registrar
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

    private function registrarEntrada(int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void
    {
        $facturaUrl = $this->gestorFacturas->guardar($this->arrayDesdeUploadedFile($factura));
        (new Entradas)->crearConFacturaUrl($userId, $nombre, $monto, $fecha, $facturaUrl);
    }

    private function registrarSalida(int $userId, string $nombre, float $monto, string $fecha, UploadedFile $factura): void
    {
        $facturaUrl = $this->gestorFacturas->guardar($this->arrayDesdeUploadedFile($factura));
        (new Salidas)->crearConFacturaUrl($userId, $nombre, $monto, $fecha, $facturaUrl);
    }

    /**
     * Formato compatible con {@see GestorFacturas::guardar()}.
     *
     * @return array{name: string, type: string, tmp_name: string, error: int, size: int}
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
