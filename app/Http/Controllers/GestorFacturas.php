<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestorFacturas extends Controller
{
    /**
     * Esta función recibe la solicitud de la web, extrae el archivo
     * y lo guarda en la carpeta storage del servidor.
     */
    public function guardarFactura(Request $request)
    {
        // 1. Verificamos si en el formulario se envió un archivo llamado 'factura_archivo'
        if ($request->hasFile('factura_archivo')) {
            
            // 2. Lo guardamos en 'storage/app/public/facturas'
            $ruta = $request->file('factura_archivo')->store('facturas', 'public');
            
            // 3. Retornamos la ruta para que la Persona 1 la guarde en la base de datos
            return $ruta;
        }

        return null; // Si no hay archivo, no hace nada
    }
}