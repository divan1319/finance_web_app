@extends('layouts.app')

@section('brand', 'Salidas')
@section('title')
<h1 class="text-3xl font-bold tracking-tight text-white">
    Salidas
</h1>
@endsection

@section('boton_accion')
    <a href="{{ route('dashboard.salidas.registro.index') }}" class="bg-white text-gray-900 px-4 py-2 rounded-md text-sm font-bold">Registrar salida</a>
@endsection

@section('content')

@endsection