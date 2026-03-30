@extends('layouts.app')

@section('brand', 'Detalle de salida')
@section('title')
    <h1 class="text-3xl font-bold tracking-tight text-white">
        Detalle de salida
    </h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.salidas.index') }}"
        class="rounded-md border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Volver
        a salidas</a>
@endsection

@section('content')
    @if (session('ok'))
        <div class="mb-6 rounded-md bg-emerald-500/10 px-3 py-2 text-sm text-emerald-200 outline outline-1 outline-emerald-500/30"
            role="status">
            {{ session('ok') }}
        </div>
    @endif
    <x-gasto-detalle :registro="$registro" tipo="salida" />
@endsection
