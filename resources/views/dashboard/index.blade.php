@extends('layouts.app')

@section('brand', 'Dashboard')

@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Dashboard
</h1>
@endsection

@section('content')
<div class="p-6">
    @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
    <h2 class="text-xl font-bold text-white mb-4">Registrar Movimiento</h2>

    <form action="{{ route('gastos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <input type="hidden" name="tipo_registro_id" value="2"> 

        <div class="mb-4">
            <label class="block text-gray-300">Concepto / Nombre</label>
            <input type="text" name="nombre" required class="w-full rounded bg-gray-800 text-white p-2 border border-gray-700">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-300">Monto ($)</label>
                <input type="number" name="monto" step="0.01" required class="w-full rounded bg-gray-800 text-white p-2 border border-gray-700">
            </div>
            <div>
                <label class="block text-gray-300">Fecha</label>
                <input type="date" name="fecha" required class="w-full rounded bg-gray-800 text-white p-2 border border-gray-700">
            </div>
        </div>

        <div>
            <label class="block text-gray-300">Foto de Factura (Opcional)</label>
            <div>
    <div class="flex items-center justify-center w-full my-6">
        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-800 hover:bg-gray-700 hover:border-blue-500 transition-all duration-300">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="mb-2 text-sm text-gray-400 font-semibold">Haz clic para subir</p>
                <p class="text-xs text-gray-500 uppercase">JPG, PNG o JPEG (MAX. 2MB)</p>
            </div>
            <input type="file" name="factura" accept="image/*" class="hidden" />
        </label>
    </div>
</div>
        </div>
        @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-400 bg-gray-800 rounded-lg">
        <p class="font-bold">⚠️ Revisa los siguientes errores:</p>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
         </div>
        @endif
        <button type="submit" class="mb-10 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all">
    Guardar en Base de Datos
</button>
    </form>
    <hr class="border-gray-700 my-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
    <div class="bg-gray-800 border-l-4 border-blue-500 p-4 rounded shadow">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500/10 rounded-full">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider">Total Acumulado</p>
                <p class="text-2xl font-bold text-white">${{ number_format($totalGastos, 2) }}</p>
            </div>
        </div>
    </div>
</div>
<h2 class="text-xl font-bold text-white mb-4">Historial de Movimientos</h2>

<div class="overflow-x-auto">
    <table class="w-full text-left text-gray-300">
        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
            <tr>
                <th class="p-3">Fecha</th>
                <th class="p-3">Tipo</th> <th class="p-3">Concepto</th>
                <th class="p-3">Monto</th>
                <th class="p-3 text-center">Factura</th>
                <th class="p-3 text-center">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @foreach($gastos as $gasto)
            <tr class="hover:bg-gray-800/50">
                <td class="p-3 text-sm">{{ $gasto->fecha }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-xs font-bold {{ $gasto->tipo_registro_id == 1 ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                        {{ $gasto->tipo->nombre ?? 'N/A' }}
                    </span>
                </td>
                <td class="p-3 text-sm">{{ $gasto->nombre }}</td>
                <td class="p-3 font-semibold text-white">${{ number_format($gasto->monto, 2) }}</td>
                <td class="p-3 text-center">
                    @if($gasto->factura_url)
                       <a href="{{ asset($gasto->factura_url) }}" target="_blank" class="flex items-center hover:text-blue-400">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Ver
                        </a>
                    @else
                        <span class="text-gray-600 italic text-xs">Sin factura</span>
                    @endif
                    </td> <td class="p-3 text-center">
                        <form action="{{ route('gastos.destroy', $gasto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este registro?')">
                  @csrf
                  @method('DELETE')
                     <button type="submit" class="text-red-500 hover:text-red-400">
                     <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
