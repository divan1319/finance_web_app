<?php

namespace App\Database;

/**
 * Clase ReporteBalance
 *
 * Encargada de generar informes financieros combinando entradas y salidas.
 */
class ReporteBalance
{
    private Entradas $entradas;

    private Salidas $salidas;

    public function __construct()
    {
        $this->entradas = new Entradas;
        $this->salidas = new Salidas;
    }

    /**
     * Obtiene un resumen completo del balance para un usuario.
     *
     * @param  int  $userId  ID del usuario
     * @return array Resumen con total entradas, total salidas y balance neto
     */
    public function obtenerResumen(int $userId): array
    {
        $totalEntradas = $this->entradas->total($userId);
        $totalSalidas = $this->salidas->total($userId);
        $balanceNeto = $totalEntradas - $totalSalidas;

        return [
            'total_entradas' => $totalEntradas,
            'total_salidas' => $totalSalidas,
            'balance_neto' => $balanceNeto,
            'estado' => $balanceNeto >= 0 ? 'Superávit' : 'Déficit',
        ];
    }

    /**
     * Obtiene el balance neto directamente.
     *
     * @param  int  $userId  ID del usuario
     * @return float Balance neto (Entradas - Salidas)
     */
    public function calcularBalanceNeto(int $userId): float
    {
        return $this->entradas->total($userId) - $this->salidas->total($userId);
    }

    /**
     * Genera un reporte detallado con historial cronológico.
     *
     * @param  int  $userId  ID del usuario
     * @return array Historial combinado de entradas y salidas
     */
    public function obtenerHistorialCombinado(int $userId): array
    {
        $listaEntradas = $this->entradas->listar($userId);
        $listaSalidas = $this->salidas->listar($userId);

        // Marcamos cada registro para identificarlos
        foreach ($listaEntradas as &$e) {
            $e['tipo'] = 'entrada';
        }
        foreach ($listaSalidas as &$s) {
            $s['tipo'] = 'salida';
        }

        $historial = array_merge($listaEntradas, $listaSalidas);

        // Ordenamos por fecha descendente
        usort($historial, function ($a, $b) {
            return strcmp($b['fecha'], $a['fecha']);
        });

        return $historial;
    }

    /**
     * Listados y totales filtrados por rango de fechas (inclusive, formato Y-m-d).
     *
     * @return array{
     *     entradas: array<int, array<string, mixed>>,
     *     salidas: array<int, array<string, mixed>>,
     *     total_entradas: float,
     *     total_salidas: float,
     *     balance_neto: float,
     *     porcentaje_entradas: float,
     *     porcentaje_salidas: float,
     *     total_movimientos: float
     * }
     */
    public function obtenerReportePeriodo(int $userId, string $desde, string $hasta): array
    {
        $listaEntradas = $this->entradas->listar($userId);
        $listaSalidas = $this->salidas->listar($userId);

        $entradas = array_values(array_filter(
            $listaEntradas,
            static fn (array $r): bool => $r['fecha'] >= $desde && $r['fecha'] <= $hasta
        ));
        $salidas = array_values(array_filter(
            $listaSalidas,
            static fn (array $r): bool => $r['fecha'] >= $desde && $r['fecha'] <= $hasta
        ));

        $totalEntradas = array_sum(array_map(static fn (array $r): float => (float) $r['monto'], $entradas));
        $totalSalidas = array_sum(array_map(static fn (array $r): float => (float) $r['monto'], $salidas));
        $balanceNeto = $totalEntradas - $totalSalidas;
        $totalMov = $totalEntradas + $totalSalidas;

        if ($totalMov > 0) {
            $porcE = round(($totalEntradas / $totalMov) * 100, 1);
            $porcS = round(100 - $porcE, 1);
        } else {
            $porcE = 0.0;
            $porcS = 0.0;
        }

        return [
            'entradas' => $entradas,
            'salidas' => $salidas,
            'total_entradas' => $totalEntradas,
            'total_salidas' => $totalSalidas,
            'balance_neto' => $balanceNeto,
            'porcentaje_entradas' => $porcE,
            'porcentaje_salidas' => $porcS,
            'total_movimientos' => $totalMov,
        ];
    }
}
