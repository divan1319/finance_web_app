<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoRegistroSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo_registros')->insertOrIgnore([
            [
                'nombre'      => 'Registro de entrada',
                'codigo'      => 'registro_entrada',
                'descripcion' => 'Ingresos o entradas de dinero.',
                'activo'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Registro de salida',
                'codigo'      => 'registro_salida',
                'descripcion' => 'Egresos o salidas de dinero.',
                'activo'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
