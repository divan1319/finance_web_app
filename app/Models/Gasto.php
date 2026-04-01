<?php

namespace App\Models;

use App\Models\TipoRegistro;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    // Definimos el nombre de la tabla si es diferente al plural del modelo
    protected $table = 'gastos';

    // Lista de columnas que permitimos llenar desde el formulario
    protected $fillable = [
        'tipo_registro_id', 
        'user_id', 
        'nombre', 
        'monto', 
        'fecha', 
        'factura_url'
    ];

    public function tipo()
{
    return $this->belongsTo(TipoRegistro::class, 'tipo_registro_id');
}
}