@extends('layouts.app')

@section('brand', 'Balance')
@section('title')
    <h1 class="text-3xl font-bold tracking-tight text-white">
        Balance del periodo
    </h1>
@endsection

@section('boton_accion')
    <div class="flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('dashboard.balance.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}"
            class="rounded-md bg-white px-4 py-2 text-sm font-bold text-gray-900 hover:bg-gray-100">Exportar PDF</a>
        <a href="{{ route('dashboard.index') }}"
            class="rounded-md border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Volver
            al dashboard</a>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <form method="GET" action="{{ route('dashboard.balance') }}"
            class="flex flex-wrap items-end gap-4 rounded-lg border border-white/10 bg-white/5 p-4">
            <div>
                <label for="desde" class="block text-xs font-medium text-gray-400">Desde</label>
                <input id="desde" type="date" name="desde" value="{{ $desde }}" required
                    class="mt-1 rounded-md border-0 bg-white/10 px-3 py-2 text-sm text-white scheme-dark focus:ring-2 focus:ring-white/30">
            </div>
            <div>
                <label for="hasta" class="block text-xs font-medium text-gray-400">Hasta</label>
                <input id="hasta" type="date" name="hasta" value="{{ $hasta }}" required
                    class="mt-1 rounded-md border-0 bg-white/10 px-3 py-2 text-sm text-white scheme-dark focus:ring-2 focus:ring-white/30">
            </div>
            <button type="submit"
                class="rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-400">Aplicar
                periodo</button>
        </form>

        <p class="text-center text-lg text-gray-300">
            Periodo: <span class="font-semibold text-white">{{ $desdeLabel }}</span>
            —
            <span class="font-semibold text-white">{{ $hastaLabel }}</span>
        </p>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="overflow-hidden rounded-lg border border-emerald-500/30 bg-white/5">
                <div class="border-b border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-center text-sm font-semibold text-emerald-200">
                    Entradas</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-white/10 text-gray-400">
                            <tr>
                                <th class="px-4 py-2 font-medium">Concepto</th>
                                <th class="px-4 py-2 text-right font-medium">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($reporte['entradas'] as $fila)
                                <tr>
                                    <td class="px-4 py-2 text-white">{{ $fila['nombre'] }}</td>
                                    <td class="px-4 py-2 text-right text-gray-200">
                                        ${{ number_format((float) $fila['monto'], 2, '.', ',') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500">Sin movimientos</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-t border-emerald-500/30 bg-emerald-500/5">
                            <tr>
                                <td class="px-4 py-3 font-bold text-emerald-200">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-100">
                                    ${{ number_format($reporte['total_entradas'], 2, '.', ',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-rose-500/30 bg-white/5">
                <div class="border-b border-rose-500/20 bg-rose-500/10 px-4 py-2 text-center text-sm font-semibold text-rose-200">
                    Salidas</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-white/10 text-gray-400">
                            <tr>
                                <th class="px-4 py-2 font-medium">Concepto</th>
                                <th class="px-4 py-2 text-right font-medium">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($reporte['salidas'] as $fila)
                                <tr>
                                    <td class="px-4 py-2 text-white">{{ $fila['nombre'] }}</td>
                                    <td class="px-4 py-2 text-right text-gray-200">
                                        ${{ number_format((float) $fila['monto'], 2, '.', ',') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500">Sin movimientos</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-t border-rose-500/30 bg-rose-500/5">
                            <tr>
                                <td class="px-4 py-3 font-bold text-rose-200">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-rose-100">
                                    ${{ number_format($reporte['total_salidas'], 2, '.', ',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-6 text-center text-xl font-semibold text-white">
            Balance del periodo:
            <span @class([
                'ml-2',
                'text-emerald-400' => $reporte['balance_neto'] >= 0,
                'text-rose-400' => $reporte['balance_neto'] < 0,
            ])>${{ number_format($reporte['balance_neto'], 2, '.', ',') }}</span>
            <span class="ml-2 text-sm font-normal text-gray-400">(entradas − salidas)</span>
        </div>

        <div class="overflow-hidden rounded-lg border border-white/10 bg-white/5 p-6">
            <h2 class="mb-6 text-center text-lg font-semibold text-white">Gráfico: entradas vs salidas</h2>
            @if ($svgPastel)
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:justify-center sm:gap-10">
                    <div class="shrink-0 text-emerald-400 [&_svg]:block">
                        {!! $svgPastel !!}
                    </div>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center gap-2">
                            <span class="inline-block size-3 rounded-sm bg-emerald-500"></span>
                            Entradas: {{ $reporte['porcentaje_entradas'] }}%
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-block size-3 rounded-sm bg-rose-500"></span>
                            Salidas: {{ $reporte['porcentaje_salidas'] }}%
                        </li>
                    </ul>
                </div>
                <p class="mt-4 text-center text-xs text-gray-500">Porcentaje sobre el total de movimientos del periodo
                    (entradas + salidas).</p>
            @else
                <p class="text-center text-gray-500">No hay movimientos en este periodo; no se puede dibujar el gráfico.
                </p>
            @endif
        </div>
    </div>
@endsection
