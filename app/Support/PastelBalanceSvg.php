<?php

namespace App\Support;

/**
 * Gráfico circular (pastel) entradas vs salidas como fragmento SVG inline.
 *
 * Colores: verde (entradas) y rojo (salidas). Útil en vistas HTML; en PDF puede preferirse
 * {@see PastelBalancePng} según el motor de renderizado.
 */
final class PastelBalanceSvg
{
    /**
     * @param  float  $porcentajeEntradas  Porcentaje del pastel para entradas (0–100).
     * @param  int  $tamano  Ancho y alto del viewBox en unidades SVG.
     * @return string Markup SVG (sin XML declaration) con dos sectores o círculo lleno en casos límite.
     */
    public static function generar(float $porcentajeEntradas, int $tamano = 220): string
    {
        $pe = max(0.0, min(100.0, $porcentajeEntradas));
        $r = ($tamano / 2) - 12;
        $cx = $tamano / 2;
        $cy = $tamano / 2;
        $verde = '#10b981';
        $rojo = '#f43f5e';

        if ($pe <= 0.0001) {
            return self::circuloLleno($cx, $cy, $r, $rojo, $tamano);
        }
        if ($pe >= 99.9999) {
            return self::circuloLleno($cx, $cy, $r, $verde, $tamano);
        }

        $inicio = deg2rad(-90);
        $finEntradas = deg2rad(-90 + 360 * $pe / 100);

        $x1 = $cx + $r * cos($inicio);
        $y1 = $cy + $r * sin($inicio);
        $x2 = $cx + $r * cos($finEntradas);
        $y2 = $cy + $r * sin($finEntradas);

        $gran1 = (360 * $pe / 100) > 180 ? 1 : 0;
        $gran2 = (360 * (100 - $pe) / 100) > 180 ? 1 : 0;

        $pathE = sprintf(
            'M %.2f %.2f L %.2f %.2f A %.2f %.2f 0 %d 1 %.2f %.2f Z',
            $cx,
            $cy,
            $x1,
            $y1,
            $r,
            $r,
            $gran1,
            $x2,
            $y2
        );
        $pathS = sprintf(
            'M %.2f %.2f L %.2f %.2f A %.2f %.2f 0 %d 1 %.2f %.2f Z',
            $cx,
            $cy,
            $x2,
            $y2,
            $r,
            $r,
            $gran2,
            $x1,
            $y1
        );

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d" role="img" aria-label="Gráfico entradas vs salidas">'
            .'<path fill="%s" d="%s"/><path fill="%s" d="%s"/></svg>',
            $tamano,
            $tamano,
            $tamano,
            $tamano,
            $verde,
            $pathE,
            $rojo,
            $pathS
        );
    }

    /**
     * SVG de un círculo sólido cuando el porcentaje es prácticamente 0 % o 100 %.
     */
    private static function circuloLleno(float $cx, float $cy, float $r, string $color, int $tamano): string
    {
        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">'
            .'<circle cx="%.2f" cy="%.2f" r="%.2f" fill="%s"/></svg>',
            $tamano,
            $tamano,
            $tamano,
            $tamano,
            $cx,
            $cy,
            $r,
            $color
        );
    }
}
