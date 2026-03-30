<?php

namespace App\Support;

/**
 * Gráfico circular (pastel) entradas vs salidas como PNG embebido en data URI.
 *
 * Pensado para DomPDF, que no renderiza bien SVG en muchos casos. Requiere la extensión GD;
 * si no está disponible o falla la generación, devuelve null.
 */
final class PastelBalancePng
{
    /**
     * Genera un PNG en base64 con prefijo `data:image/png;base64,...`.
     *
     * @param  float  $porcentajeEntradas  Porcentaje del círculo dedicado a entradas (0–100).
     * @param  int  $tamano  Lado del cuadrado en píxeles (se acota entre 80 y 400).
     * @return string|null Data URI listo para el atributo `src` en HTML/PDF, o null sin GD o si falla GD.
     */
    public static function generarDataUri(float $porcentajeEntradas, int $tamano = 200): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $pe = max(0.0, min(100.0, $porcentajeEntradas));
        $tamano = max(80, min(400, $tamano));

        $img = imagecreatetruecolor($tamano, $tamano);
        if ($img === false) {
            return null;
        }

        imagesavealpha($img, true);
        $transparente = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $transparente);

        $verde = imagecolorallocate($img, 16, 185, 129);
        $rojo = imagecolorallocate($img, 244, 63, 94);

        $cx = (int) ($tamano / 2);
        $cy = (int) ($tamano / 2);
        $d = $tamano - 8;

        if ($pe <= 0.0001) {
            imagefilledellipse($img, $cx, $cy, $d, $d, $rojo);
        } elseif ($pe >= 99.9999) {
            imagefilledellipse($img, $cx, $cy, $d, $d, $verde);
        } else {
            $sweepEnt = (int) round(360 * $pe / 100);
            $endGreen = 270 + $sweepEnt;

            if ($endGreen < 360) {
                imagefilledarc($img, $cx, $cy, $d, $d, 270, $endGreen, $verde, IMG_ARC_PIE);
                imagefilledarc($img, $cx, $cy, $d, $d, $endGreen, 360, $rojo, IMG_ARC_PIE);
                imagefilledarc($img, $cx, $cy, $d, $d, 0, 270, $rojo, IMG_ARC_PIE);
            } elseif ($endGreen === 360) {
                imagefilledarc($img, $cx, $cy, $d, $d, 270, 360, $verde, IMG_ARC_PIE);
                imagefilledarc($img, $cx, $cy, $d, $d, 0, 270, $rojo, IMG_ARC_PIE);
            } else {
                imagefilledarc($img, $cx, $cy, $d, $d, 270, 360, $verde, IMG_ARC_PIE);
                imagefilledarc($img, $cx, $cy, $d, $d, 0, $endGreen - 360, $verde, IMG_ARC_PIE);
                imagefilledarc($img, $cx, $cy, $d, $d, $endGreen - 360, 270, $rojo, IMG_ARC_PIE);
            }
        }

        ob_start();
        imagepng($img, null, 6);
        $binary = ob_get_clean();
        imagedestroy($img);

        if ($binary === false || $binary === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($binary);
    }
}
