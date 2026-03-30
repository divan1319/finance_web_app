<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @yield('html_attrs') class="h-full bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('brand', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @stack('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="@yield('body_class', 'h-full')">
    <div class="min-h-full">
        <nav class="bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=gray&shade=100"
                                alt="Your Company" class="size-8" />
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('dashboard.index') }}"
                                    @class([
                                        'rounded-md px-3 py-2 text-sm font-medium',
                                        'bg-gray-950/50 text-white' => request()->routeIs('dashboard.index'),
                                        'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.index'),
                                    ])
                                    @if (request()->routeIs('dashboard.index')) aria-current="page" @endif>Dashboard</a>
                                <a href="{{ route('dashboard.entradas.index') }}"
                                    @class([
                                        'rounded-md px-3 py-2 text-sm font-medium',
                                        'bg-gray-950/50 text-white' => request()->routeIs('dashboard.entradas.*'),
                                        'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.entradas.*'),
                                    ])
                                    @if (request()->routeIs('dashboard.entradas.*')) aria-current="page" @endif>Entradas</a>
                                <a href="{{ route('dashboard.salidas.index') }}"
                                    @class([
                                        'rounded-md px-3 py-2 text-sm font-medium',
                                        'bg-gray-950/50 text-white' => request()->routeIs('dashboard.salidas.*'),
                                        'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.salidas.*'),
                                    ])
                                    @if (request()->routeIs('dashboard.salidas.*')) aria-current="page" @endif>Salidas</a>
 
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"
                                    class="relative inline-flex cursor-pointer items-center gap-2 rounded-full p-1.5 text-sm font-medium text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-white">
                                    <span class="absolute -inset-1.5"></span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        aria-hidden="true" class="size-6 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                    </svg>
                                    <span class="hidden pr-1 sm:inline">Cerrar sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="-mr-2 flex items-center gap-2 md:hidden">

                        <button type="button" id="mobile-menu-toggle"
                            class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Abrir menú principal</span>
                            <svg id="mobile-menu-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" aria-hidden="true" class="size-6">
                                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <svg id="mobile-menu-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" aria-hidden="true" class="hidden size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="mobile-menu" class="hidden border-t border-white/10 md:hidden">
                <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                    <a href="{{ route('dashboard.index') }}"
                        @class([
                            'block rounded-md px-3 py-2 text-base font-medium',
                            'bg-gray-950/50 text-white' => request()->routeIs('dashboard.index'),
                            'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.index'),
                        ])
                        @if (request()->routeIs('dashboard.index')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('dashboard.entradas.index') }}"
                        @class([
                            'block rounded-md px-3 py-2 text-base font-medium',
                            'bg-gray-950/50 text-white' => request()->routeIs('dashboard.entradas.*'),
                            'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.entradas.*'),
                        ])
                        @if (request()->routeIs('dashboard.entradas.*')) aria-current="page" @endif>Entradas</a>
                    <a href="{{ route('dashboard.salidas.index') }}"
                        @class([
                            'block rounded-md px-3 py-2 text-base font-medium',
                            'bg-gray-950/50 text-white' => request()->routeIs('dashboard.salidas.*'),
                            'text-gray-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.salidas.*'),
                        ])
                        @if (request()->routeIs('dashboard.salidas.*')) aria-current="page" @endif>Salidas</a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"
                            class="relative inline-flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-2 text-sm font-medium text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-white">
                            <span class="absolute -inset-0.5"></span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                aria-hidden="true" class="size-6 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <header
            class="relative bg-gray-800 after:pointer-events-none after:absolute after:inset-x-0 after:inset-y-0 after:border-y after:border-white/10">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
                <div>
                @yield('title')
                </div>
                <div>
                    @yield('boton_accion')
                </div>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
