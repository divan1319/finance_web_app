@extends('layouts.app')

@section('brand', 'Entradas')
@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Entradas
</h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.entradas.registro.entrada') }}" class="bg-white text-gray-900 px-4 py-2 rounded-md text-sm font-bold">Registrar entrada</a>
@endsection

@section('content')
    @if (session('ok'))
        <div class="mb-6 rounded-md bg-emerald-500/10 px-3 py-2 text-sm text-emerald-200 outline outline-1 outline-emerald-500/30"
            role="status">
            {{ session('ok') }}
        </div>
    @endif
@endsection