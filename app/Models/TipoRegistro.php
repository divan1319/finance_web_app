<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRegistro extends Model
{
    // Especificamos el nombre exacto de tu tabla
    protected $table = 'tipo_registros';

    // Si no vas a usar created_at/updated_at en esta tabla, puedes ponerlo en false
    // pero como veo que los tienes en la estructura, déjalos por defecto.
}