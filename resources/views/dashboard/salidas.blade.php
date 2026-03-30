@extends('layouts.app')

@section('brand', 'Salidas')
@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Salidas
</h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.salidas.registro.salida') }}" class="bg-white text-gray-900 px-4 py-2 rounded-md text-sm font-bold">Registrar salida</a>
@endsection

@section('content')
    @if (session('ok'))
        <div class="mb-6 rounded-md bg-emerald-500/10 px-3 py-2 text-sm text-emerald-200  outline-1 outline-emerald-500/30"
            role="status">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-500/10 px-3 py-2 text-sm text-red-200  outline-1 outline-red-500/30"
            role="alert">
            {{ session('error') }}
        </div>
    @endif

    <x-tabla-gastos :gastos="$gastos" tipo="salida" />
@endsection
