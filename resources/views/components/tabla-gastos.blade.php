@php
    $rutaDetalle = $tipo === 'entrada' ? 'dashboard.entradas.show' : 'dashboard.salidas.show';
    $rutaEliminar = $tipo === 'entrada' ? 'dashboard.entradas.destroy' : 'dashboard.salidas.destroy';
@endphp

<div class="overflow-hidden rounded-lg border border-white/10 bg-white/5">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10 text-left text-sm">
            <thead class="bg-white/5">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold text-gray-200">Descripción</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-gray-200">Monto</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-gray-200">Fecha</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-gray-200">Registrado</th>
                    <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-200">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($gastos as $fila)
                    <tr class="hover:bg-white/3">
                        <td class="max-w-xs truncate px-4 py-3 text-white">{{ $fila['nombre'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-200">
                           ${{ number_format((float) $fila['monto'], 2, '.', ',') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-300">
                            {{ \Illuminate\Support\Carbon::parse($fila['fecha'])->format('d/m/Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-400">
                            {{ \Illuminate\Support\Carbon::parse($fila['created_at'])->format('d/m/Y H:i') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                @if ($tipo === 'entrada')
                                    <a href="{{ route($rutaDetalle, $fila['id']) }}"
                                        class="rounded-md bg-emerald-500/20 px-2.5 py-1 text-xs font-semibold text-emerald-200 ring-1 ring-inset ring-emerald-500/30 hover:bg-emerald-500/30">Ver
                                        detalle</a>
                                @else
                                    <a href="{{ route($rutaDetalle, $fila['id']) }}"
                                        class="rounded-md bg-rose-500/20 px-2.5 py-1 text-xs font-semibold text-rose-200 ring-1 ring-inset ring-rose-500/30 hover:bg-rose-500/30">Ver
                                        detalle</a>
                                @endif
                                <form action="{{ route($rutaEliminar, $fila['id']) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este registro? Se borrará también la imagen de la factura.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="cursor-pointer rounded-md bg-red-500/15 px-2.5 py-1 text-xs font-semibold text-red-200 ring-1 ring-inset ring-red-500/30 hover:bg-red-500/25">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay registros todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
