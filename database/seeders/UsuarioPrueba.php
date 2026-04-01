<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioPrueba extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\User::create([
        'name' => 'Estudiante UDB',
        'email' => 'udb@prueba.com',
        'password' => hash::make('password123'),
    ]);
}
}
