@php
    use Illuminate\Support\Carbon;
    $creado = Carbon::parse($registro['created_at'])->format('d/m/Y H:i');
    $fechaInput = old('fecha', Carbon::parse($registro['fecha'])->format('Y-m-d'));
    $idLightbox = 'factura-lightbox-' . $registro['id'];
    $idThumb = 'factura-thumb-' . $registro['id'];
    $urlFactura = asset($registro['factura_url']);
    $suffix = (string) $registro['id'];
@endphp

<div class="mx-auto max-w-3xl space-y-8">
    @if ($errors->any())
        <div class="rounded-md bg-red-500/10 px-3 py-2 text-sm text-red-200 outline outline-1 outline-red-500/30"
            role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-white/10 bg-white/5 p-6">
        <h2 class="mb-4 text-lg font-semibold text-white">Editar registro</h2>
        <p class="mb-6 text-sm text-gray-400">Modifica los datos y, si quieres, sube una nueva imagen de factura. Si no
            eliges archivo, se conserva la actual.</p>

        <form action="{{ $actionActualizar }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nombre-{{ $suffix }}" class="block text-sm/6 font-medium text-gray-100">Descripción</label>
                <div class="mt-2">
                    <input id="nombre-{{ $suffix }}" type="text" name="nombre"
                        value="{{ old('nombre', $registro['nombre']) }}" required maxlength="255" autocomplete="off"
                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 @error('nombre') outline-red-500/50 @enderror" />
                </div>
            </div>

            <div>
                <label for="monto-{{ $suffix }}" class="block text-sm/6 font-medium text-gray-100">Monto</label>
                <div class="mt-2">
                    <input id="monto-{{ $suffix }}" type="number" name="monto"
                        value="{{ old('monto', $registro['monto']) }}" required min="0.01" step="0.01"
                        inputmode="decimal"
                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 @error('monto') outline-red-500/50 @enderror" />
                </div>
            </div>

            <div>
                <label for="fecha-{{ $suffix }}" class="block text-sm/6 font-medium text-gray-100">Fecha del gasto</label>
                <div class="mt-2">
                    <input id="fecha-{{ $suffix }}" type="date" name="fecha" value="{{ $fechaInput }}" required
                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 scheme-dark @error('fecha') outline-red-500/50 @enderror" />
                </div>
            </div>

            <div>
                <label for="factura-{{ $suffix }}" class="block text-sm/6 font-medium text-gray-100">Nueva imagen de
                    factura</label>
                <p class="mt-1 text-xs text-gray-400">Opcional. JPG, PNG, GIF o WEBP. Máximo 5 MB. Si subes una nueva, la
                    anterior se elimina del servidor.</p>
                <div class="mt-2">
                    <input id="factura-{{ $suffix }}" type="file" name="factura"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="block w-full text-sm text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-white/20 @error('factura') outline outline-1 outline-red-500/50 rounded-md @enderror" />
                </div>
            </div>

            <div>
                <button type="submit"
                    @class([
                        'flex w-full justify-center rounded-md px-3 py-1.5 text-sm/6 font-semibold shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto sm:px-6',
                        'bg-emerald-500 text-white hover:bg-emerald-400' => $tipo === 'entrada',
                        'bg-rose-500 text-white hover:bg-rose-400' => $tipo === 'salida',
                    ])>
                    Guardar cambios
                </button>
            </div>
        </form>

        <p class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400">
            <span class="font-medium text-gray-500">Registrado el:</span> {{ $creado }}
        </p>
    </div>

    <div class="overflow-hidden rounded-lg border border-white/10 bg-white/5 p-6">
        <h2 class="mb-4 text-lg font-semibold text-white">Factura actual</h2>
        <p class="mb-3 text-sm text-gray-400">Pulsa la imagen para verla en grande.</p>
        <button type="button" id="{{ $idThumb }}"
            class="group block w-full max-w-md cursor-zoom-in rounded-lg ring-2 ring-transparent transition hover:ring-white/30 focus:outline-none focus-visible:ring-white/50">
            <img src="{{ $urlFactura }}" alt="Vista previa de la factura"
                class="max-h-72 w-full rounded-lg object-contain bg-black/20" width="800" height="600"
                fetchpriority="high">
            <span class="mt-2 block text-center text-xs text-gray-500 group-hover:text-gray-400">Clic para ampliar</span>
        </button>
    </div>
</div>

<dialog id="{{ $idLightbox }}"
    class="gasto-factura-lightbox m-0 flex h-full max-h-none w-full max-w-none items-center justify-center border-0 bg-black/85 p-4 sm:p-8 text-white backdrop:bg-black/80">
    <div data-lightbox-panel
        class="flex max-h-[90vh] w-full max-w-5xl flex-col gap-3 overflow-hidden rounded-lg border border-white/20 bg-gray-950 p-4 shadow-2xl">
        <div class="flex shrink-0 justify-end gap-2">
            <form method="dialog">
                <button type="submit"
                    class="rounded-md bg-white/10 px-3 py-1.5 text-sm font-medium text-white hover:bg-white/20">Cerrar</button>
            </form>
        </div>
        <div class="min-h-0 flex-1 overflow-auto">
            <img src="{{ $urlFactura }}" alt="Factura ampliada" data-lightbox-img
                class="mx-auto max-h-[80vh] w-auto max-w-full object-contain">
        </div>
    </div>
</dialog>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[id^="factura-thumb-"]').forEach(function(thumb) {
                var id = thumb.id.replace('factura-thumb-', '');
                var dialog = document.getElementById('factura-lightbox-' + id);
                if (!dialog) return;
                thumb.addEventListener('click', function() {
                    dialog.showModal();
                });
                var panel = dialog.querySelector('[data-lightbox-panel]');
                dialog.addEventListener('click', function() {
                    dialog.close();
                });
                if (panel) {
                    panel.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });
        </script>
    @endpush
@endonce
