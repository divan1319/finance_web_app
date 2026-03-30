<?php

namespace App\Http\Controllers\Dashboard;

use App\Database\ReporteBalance;
use App\Http\Controllers\Controller;
use App\Support\PastelBalancePng;
use App\Support\PastelBalanceSvg;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BalanceController extends Controller
{
    public function show(Request $request): View
    {
        [$desde, $hasta] = $this->resolverPeriodo($request);

        $reporte = (new ReporteBalance)->obtenerReportePeriodo(
            (int) $request->user()->id,
            $desde,
            $hasta
        );

        $svgPastel = $reporte['total_movimientos'] > 0
            ? PastelBalanceSvg::generar($reporte['porcentaje_entradas'])
            : null;

        return view('dashboard.balance', [
            'desde' => $desde,
            'hasta' => $hasta,
            'desdeLabel' => Carbon::parse($desde)->format('d/m/Y'),
            'hastaLabel' => Carbon::parse($hasta)->format('d/m/Y'),
            'reporte' => $reporte,
            'svgPastel' => $svgPastel,
        ]);
    }

    public function pdf(Request $request): Response
    {
        [$desde, $hasta] = $this->resolverPeriodo($request);

        $reporte = (new ReporteBalance)->obtenerReportePeriodo(
            (int) $request->user()->id,
            $desde,
            $hasta
        );

        $pastelPdf = $reporte['total_movimientos'] > 0
            ? PastelBalancePng::generarDataUri($reporte['porcentaje_entradas'], 200)
            : null;

        $pdf = Pdf::loadView('dashboard.pdf.balance-report', [
            'desdeLabel' => Carbon::parse($desde)->format('d/m/Y'),
            'hastaLabel' => Carbon::parse($hasta)->format('d/m/Y'),
            'reporte' => $reporte,
            'pastelPdf' => $pastelPdf,
        ])->setPaper('a4', 'portrait');

        $pdf->setOption('isRemoteEnabled', true);

        $nombreArchivo = sprintf('balance-%s-%s.pdf', $desde, $hasta);

        return $pdf->download($nombreArchivo);
    }

    /**
     * @return array{0: string, 1: string} Fechas Y-m-d
     */
    private function resolverPeriodo(Request $request): array
    {
        $desdeInput = $request->query('desde');
        $hastaInput = $request->query('hasta');

        if ($desdeInput && $hastaInput) {
            try {
                $desde = Carbon::parse($desdeInput)->format('Y-m-d');
                $hasta = Carbon::parse($hastaInput)->format('Y-m-d');
                if ($desde <= $hasta) {
                    return [$desde, $hasta];
                }
            } catch (\Throwable) {
                // cae al mes actual
            }
        }

        $inicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $fin = Carbon::now()->endOfMonth()->format('Y-m-d');

        return [$inicio, $fin];
    }
}
