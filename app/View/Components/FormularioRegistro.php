<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

class FormularioRegistro extends Component
{
    public string $action;

    public string $etiquetaBoton;

    public function __construct(
        public string $codigo,
    ) {
        if (! in_array($codigo, ['registro_entrada', 'registro_salida'], true)) {
            throw new InvalidArgumentException('codigo debe ser registro_entrada o registro_salida.');
        }

        $this->action = match ($codigo) {
            'registro_entrada' => route('dashboard.entradas.registro.store'),
            'registro_salida' => route('dashboard.salidas.registro.store'),
        };

        $this->etiquetaBoton = match ($codigo) {
            'registro_entrada' => 'Registrar entrada',
            'registro_salida' => 'Registrar salida',
        };
    }

    public function esEntrada(): bool
    {
        return $this->codigo === 'registro_entrada';
    }

    public function render(): View
    {
        return view('components.formulario-registro');
    }
}
