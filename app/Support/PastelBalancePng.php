<?php

namespace App\Support;

/**
 * Pastel entradas/salidas como PNG (data URI) para dompdf, que no dibuja bien SVG.
 */
final class PastelBalancePng
{
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
