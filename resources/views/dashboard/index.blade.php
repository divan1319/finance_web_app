@extends('layouts.app')

@section('brand', 'Dashboard')

@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Dashboard - Bienvenido, {{ auth()->user()->name }}
</h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.balance') }}"
        class="rounded-md bg-white px-4 py-2 text-sm font-bold text-gray-900 hover:bg-gray-100">Mostrar balance</a>
@endsection

@section('content')
    <div class="rounded-lg border border-white/10 bg-white/5 p-6 text-gray-300">
        <p class="text-sm leading-relaxed">
            Consulta el resumen de <strong class="text-white">entradas</strong> y <strong class="text-white">salidas</strong>
            del periodo, el balance neto y un gráfico comparativo. Puedes ajustar las fechas y exportar el informe en PDF.
        </p>
    </div>
@endsection
