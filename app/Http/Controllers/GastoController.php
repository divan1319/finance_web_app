<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // 1. Obtenemos todos los gastos del usuario logueado
    $gastos = Gasto::where('user_id', auth()->id())->get();

    // 2. Calculamos la suma total de la columna 'monto'
    $totalGastos = $gastos->sum('monto');

    // 3. Enviamos AMBAS variables a la vista
    return view('dashboard.index', compact('gastos', 'totalGastos'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1. Validar datos y el archivo
    // Sustituye el bloque validate actual por este:
$request->validate([
    'nombre' => 'required|string|max:255',
    'monto' => 'required|numeric|min:0.01',
    'fecha' => 'required|date',
    'tipo_registro_id' => 'required|exists:tipo_registros,id', // Corregido a "registro"
    'factura' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
], [
    'nombre.required' => 'El concepto o nombre es obligatorio.',
    'monto.required' => 'Debes ingresar un monto válido.',
    'monto.min' => 'El monto debe ser mayor a cero.',
    'fecha.required' => 'La fecha es obligatoria.',
    'tipo_registro_id.required' => 'Falta el tipo de registro.',
    'factura.image' => 'El archivo debe ser una imagen (JPG, PNG).',
    'factura.max' => 'La imagen es muy pesada (máximo 2MB).',
]);

    $rutaArchivo = null;

    // 2. Gestión de Archivos: Subir la foto si existe
    if ($request->hasFile('factura')) {
        $nombreArchivo = time() . '.' . $request->factura->extension();
        $request->factura->move(public_path('uploads/facturas'), $nombreArchivo);
        $rutaArchivo = 'uploads/facturas/' . $nombreArchivo;
    }

    // 3. Crear el registro en la DB
    \App\Models\Gasto::create([
        'tipo_registro_id' => $request->tipo_registro_id,
        'user_id' => auth()->id(), 
        'nombre' => $request->nombre,
        'monto' => $request->monto,
        'fecha' => $request->fecha,
        // Si $rutaArchivo es null, guardará un texto vacío en lugar de dar error
        'factura_url' => $rutaArchivo ?? '', 
    ]);

    return redirect()->route('dashboard.index')->with('success', 'Registro guardado');
}

    /**
     * Display the specified resource.
     */
    public function show(Gasto $gasto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gasto $gasto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gasto $gasto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gasto $gasto)
{
    // Elimina el registro de la base de datos
    $gasto->delete();

    // Regresa a la página anterior con un mensaje de éxito
    return redirect()->back()->with('success', 'Registro eliminado con éxito');
}
}
