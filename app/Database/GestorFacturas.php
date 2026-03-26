<?php

namespace App\Database;

use RuntimeException;

/**
 * Clase GestorFacturas
 *
 * Maneja el almacenamiento de imágenes de facturas en el servidor.
 * Guarda el archivo en la carpeta pública y retorna la ruta
 * para ser almacenada en la base de datos.
 */
class GestorFacturas
{
    private string $carpetaDestino;
    private array $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private int $tamanoMaximo = 5 * 1024 * 1024; // 5 MB

    public function __construct()
    {
        $this->carpetaDestino = public_path('facturas');
        $this->crearCarpetaSiNoExiste();
    }

    /**
     * Guarda la imagen de factura en el servidor.
     *
     * @param  array  $archivo Elemento de $_FILES (ej. $_FILES['factura'])
     * @return string          Ruta relativa para guardar en la BD (ej. 'facturas/abc123.jpg')
     *
     * @throws RuntimeException Si el archivo no es válido
     */
    public function guardar(array $archivo): string
    {
        $this->validar($archivo);

        $extension    = $this->obtenerExtension($archivo['type']);
        $nombreUnico  = uniqid('factura_', true) . '.' . $extension;
        $rutaAbsoluta = $this->carpetaDestino . DIRECTORY_SEPARATOR . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaAbsoluta)) {
            throw new RuntimeException('No se pudo guardar la factura en el servidor.');
        }

        return 'facturas/' . $nombreUnico;
    }

    /**
     * Elimina una factura del servidor dado su ruta relativa.
     *
     * @param  string $rutaRelativa Ruta almacenada en la BD (ej. 'facturas/abc123.jpg')
     * @return bool                 True si se eliminó, false si no existía
     */
    public function eliminar(string $rutaRelativa): bool
    {
        $rutaAbsoluta = public_path($rutaRelativa);

        if (file_exists($rutaAbsoluta)) {
            return unlink($rutaAbsoluta);
        }

        return false;
    }

    /**
     * Valida que el archivo sea una imagen válida y no exceda el tamaño permitido.
     *
     * @throws RuntimeException
     */
    private function validar(array $archivo): void
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error al recibir el archivo. Código: ' . $archivo['error']);
        }

        if ($archivo['size'] > $this->tamanoMaximo) {
            throw new RuntimeException('La imagen supera el tamaño máximo permitido de 5 MB.');
        }

        // Validar tipo MIME real del archivo (no solo la extensión)
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $tipoReal = $finfo->file($archivo['tmp_name']);

        if (!in_array($tipoReal, $this->tiposPermitidos, true)) {
            throw new RuntimeException('Solo se permiten imágenes JPG, PNG, GIF o WEBP.');
        }
    }

    /**
     * Retorna la extensión de archivo según el tipo MIME.
     */
    private function obtenerExtension(string $tipoMime): string
    {
        return match ($tipoMime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
    }

    /**
     * Crea la carpeta de destino si no existe.
     */
    private function crearCarpetaSiNoExiste(): void
    {
        if (!is_dir($this->carpetaDestino)) {
            mkdir($this->carpetaDestino, 0755, true);
        }
    }
}
