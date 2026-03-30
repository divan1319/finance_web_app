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
        $this->entradas = new Entradas();
        $this->salidas  = new Salidas();
    }

    /**
     * Obtiene un resumen completo del balance para un usuario.
     *
     * @param  int   $userId ID del usuario
     * @return array Resumen con total entradas, total salidas y balance neto
     */
    public function obtenerResumen(int $userId): array
    {
        $totalEntradas = $this->entradas->total($userId);
        $totalSalidas  = $this->salidas->total($userId);
        $balanceNeto   = $totalEntradas - $totalSalidas;

        return [
            'total_entradas' => $totalEntradas,
            'total_salidas'  => $totalSalidas,
            'balance_neto'   => $balanceNeto,
            'estado'         => $balanceNeto >= 0 ? 'Superávit' : 'Déficit'
        ];
    }

    /**
     * Obtiene el balance neto directamente.
     *
     * @param  int   $userId ID del usuario
     * @return float Balance neto (Entradas - Salidas)
     */
    public function calcularBalanceNeto(int $userId): float
    {
        return $this->entradas->total($userId) - $this->salidas->total($userId);
    }

    /**
     * Genera un reporte detallado con historial cronológico.
     *
     * @param  int   $userId ID del usuario
     * @return array Historial combinado de entradas y salidas
     */
    public function obtenerHistorialCombinado(int $userId): array
    {
        $listaEntradas = $this->entradas->listar($userId);
        $listaSalidas  = $this->salidas->listar($userId);

        // Marcamos cada registro para identificarlos
        foreach ($listaEntradas as &$e) { $e['tipo'] = 'entrada'; }
        foreach ($listaSalidas as &$s) { $s['tipo'] = 'salida'; }

        $historial = array_merge($listaEntradas, $listaSalidas);

        // Ordenamos por fecha descendente
        usort($historial, function ($a, $b) {
            return strcmp($b['fecha'], $a['fecha']);
        });

        return $historial;
    }
}
