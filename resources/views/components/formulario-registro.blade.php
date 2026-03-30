<div class="mx-auto max-w-lg">
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-500/10 px-3 py-2 text-sm text-red-200 outline outline-1 outline-red-500/30"
            role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label for="nombre" class="block text-sm/6 font-medium text-gray-100">Descripción</label>
            <div class="mt-2">
                <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255"
                    autocomplete="off"
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 @error('nombre') outline-red-500/50 @enderror" />
            </div>
        </div>

        <div>
            <label for="monto" class="block text-sm/6 font-medium text-gray-100">Monto</label>
            <div class="mt-2">
                <input id="monto" type="number" name="monto" value="{{ old('monto') }}" required min="0.01"
                    step="0.01" inputmode="decimal"
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 @error('monto') outline-red-500/50 @enderror" />
            </div>
        </div>

        <div>
            <label for="fecha" class="block text-sm/6 font-medium text-gray-100">Fecha</label>
            <div class="mt-2">
                <input id="fecha" type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6 scheme-dark @error('fecha') outline-red-500/50 @enderror" />
            </div>
        </div>

        <div>
            <label for="factura" class="block text-sm/6 font-medium text-gray-100">Imagen de factura</label>
            <p class="mt-1 text-xs text-gray-400">JPG, PNG, GIF o WEBP. Máximo 5 MB.</p>
            <div class="mt-2">
                <input id="factura" type="file" name="factura" required accept="image/jpeg,image/png,image/gif,image/webp"
                    class="block w-full text-sm text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-white/20 @error('factura') outline outline-1 outline-red-500/50 rounded-md @enderror" />
            </div>
        </div>

        <div>
            <button type="submit"
                @class([
                    'flex w-full justify-center rounded-md px-3 py-1.5 text-sm/6 font-semibold shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white',
                    'bg-emerald-500 text-white hover:bg-emerald-400' => $esEntrada(),
                    'bg-rose-500 text-white hover:bg-rose-400' => ! $esEntrada(),
                ])>
                {{ $etiquetaBoton }}
            </button>
        </div>
    </form>
</div>
