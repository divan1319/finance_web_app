<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

class GastoDetalle extends Component
{
    /**
     * @param  array<string, mixed>  $registro
     */
    public string $actionActualizar;

    public function __construct(
        public array $registro,
        public string $tipo,
    ) {
        if (! in_array($tipo, ['entrada', 'salida'], true)) {
            throw new InvalidArgumentException('tipo debe ser entrada o salida.');
        }

        $this->actionActualizar = match ($tipo) {
            'entrada' => route('dashboard.entradas.update', $registro['id']),
            'salida' => route('dashboard.salidas.update', $registro['id']),
        };
    }

    public function render(): View
    {
        return view('components.gasto-detalle');
    }
}
