<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

class TablaGastos extends Component
{
    public function __construct(
        /** @var array<int, array<string, mixed>> */
        public array $gastos,
        public string $tipo,
    ) {
        if (! in_array($tipo, ['entrada', 'salida'], true)) {
            throw new InvalidArgumentException('tipo debe ser entrada o salida.');
        }
    }

    public function render(): View
    {
        return view('components.tabla-gastos');
    }
}
