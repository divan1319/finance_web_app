<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8">
    <title>Balance {{ $desdeLabel }} — {{ $hastaLabel }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 24px;
            line-height: 1.4;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin: 0 0 20px;
            color: #0f172a;
        }

        h2 {
            text-align: center;
            font-size: 12px;
            margin: 20px 0 12px;
            color: #334155;
        }

        .periodo {
            text-align: center;
            margin-bottom: 16px;
            color: #475569;
        }

        .tablas {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin-bottom: 16px;
        }

        .caja-tabla {
            width: 50%;
            vertical-align: top;
            border: 1px solid #7dd3fc;
            padding: 0;
        }

        .caja-tabla h3 {
            margin: 0;
            padding: 8px;
            text-align: center;
            font-size: 11px;
            background: #e0f2fe;
            color: #0c4a6e;
            border-bottom: 1px solid #7dd3fc;
        }

        table.inner {
            width: 100%;
            border-collapse: collapse;
        }

        table.inner th,
        table.inner td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.inner th {
            text-align: left;
            color: #64748b;
            font-weight: bold;
        }

        table.inner td.monto {
            text-align: right;
        }

        table.inner tfoot td {
            font-weight: bold;
            border-bottom: none;
            border-top: 2px solid #7dd3fc;
            padding-top: 8px;
        }

        .total-entradas tfoot td {
            background: #ecfdf5;
            color: #065f46;
        }

        .total-salidas tfoot td {
            background: #fff1f2;
            color: #9f1239;
        }

        .balance {
            text-align: center;
            margin: 20px 0;
            padding: 12px;
            border: 1px solid #7dd3fc;
            font-size: 13px;
            font-weight: bold;
            background: #f8fafc;
        }

        .grafico-caja {
            border: 1px solid #7dd3fc;
            padding: 16px;
            margin-top: 8px;
            text-align: center;
        }

        .grafico-caja img.pastel-png {
            display: block;
            margin: 0 auto 12px;
        }

        .barra-pastel {
            width: 240px;
            margin: 0 auto 12px;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            height: 28px;
        }

        .barra-pastel td {
            padding: 0;
            height: 28px;
        }

        .leyenda {
            text-align: center;
            font-size: 10px;
            color: #475569;
        }

        .leyenda span {
            display: inline-block;
            margin: 0 12px;
        }

        .dot-e::before {
            content: '■ ';
            color: #10b981;
        }

        .dot-s::before {
            content: '■ ';
            color: #f43f5e;
        }

        .vacio {
            text-align: center;
            color: #94a3b8;
            padding: 12px;
        }
    </style>
</head>

<body>
    <h1>Reporte de balance</h1>
    <p class="periodo">{{ $desdeLabel }} / {{ $hastaLabel }}</p>

    <table class="tablas">
        <tr>
            <td class="caja-tabla">
                <h3>Entradas</h3>
                <table class="inner total-entradas">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th class="monto">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reporte['entradas'] as $fila)
                            <tr>
                                <td>{{ $fila['nombre'] }}</td>
                                <td class="monto">${{ number_format((float) $fila['monto'], 2, '.', ',') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="vacio">Sin movimientos</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td class="monto">${{ number_format($reporte['total_entradas'], 2, '.', ',') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td class="caja-tabla">
                <h3>Salidas</h3>
                <table class="inner total-salidas">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th class="monto">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reporte['salidas'] as $fila)
                            <tr>
                                <td>{{ $fila['nombre'] }}</td>
                                <td class="monto">${{ number_format((float) $fila['monto'], 2, '.', ',') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="vacio">Sin movimientos</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td class="monto">${{ number_format($reporte['total_salidas'], 2, '.', ',') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>

    <div class="balance">
        Balance del periodo: ${{ number_format($reporte['balance_neto'], 2, '.', ',') }} (entradas − salidas)
    </div>

    <h2>Gráfico de balance — Entradas vs Salidas</h2>
    <div class="grafico-caja">
        @if ($reporte['total_movimientos'] > 0)
            @if (! empty($pastelPdf))
                <img src="{{ $pastelPdf }}" width="200" height="200" alt="Gráfico entradas vs salidas"
                    class="pastel-png">
            @else
                <table class="barra-pastel">
                    <tr>
                        <td style="background:#10b981;width:{{ $reporte['porcentaje_entradas'] }}%;"></td>
                        <td style="background:#f43f5e;"></td>
                    </tr>
                </table>
                <p style="font-size:9px;color:#64748b;text-align:center;margin-bottom:8px;">Vista alternativa (sin
                    extensión GD)</p>
            @endif
            <p class="leyenda">
                <span class="dot-e">Entradas {{ $reporte['porcentaje_entradas'] }}%</span>
                <span class="dot-s">Salidas {{ $reporte['porcentaje_salidas'] }}%</span>
            </p>
            <p style="font-size:9px;color:#94a3b8;margin-top:8px;">Porcentaje sobre el total de movimientos del periodo.
            </p>
        @else
            <p class="vacio">No hay movimientos en el periodo.</p>
        @endif
    </div>
</body>

</html>
