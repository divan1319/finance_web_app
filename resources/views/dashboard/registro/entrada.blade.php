@extends('layouts.app')

@section('brand', 'Registro de Entrada')
@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Registro de Entrada
</h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.entradas.index') }}"
        class="rounded-md border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Volver
        a entradas</a>
@endsection

@section('content')
    <x-formulario-registro codigo="registro_entrada" />
@endsection