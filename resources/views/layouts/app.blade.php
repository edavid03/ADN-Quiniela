<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'Quiniela'))</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=archivo:400,500,600,700,800,900|sora:600,700,800" rel="stylesheet" />
        <script>
            (() => {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.dataset.theme = theme;
            })();
        </script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body data-auth="{{ auth()->check() ? 'auth' : 'guest' }}" class="min-h-screen antialiased">
        @auth
            <header class="sticky top-0 z-30 border-b border-[var(--app-border)] bg-[var(--app-panel)]/88 backdrop-blur-xl">
                <div class="app-shell flex items-center justify-between gap-2 py-2 lg:flex-wrap lg:gap-4 lg:py-3">
                    <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 no-underline md:gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] p-1 shadow-[0_4px_0_color-mix(in_srgb,var(--app-primary-strong)_70%,#000)] md:h-14 md:w-14 md:p-1.5 md:shadow-[0_8px_0_color-mix(in_srgb,var(--app-primary-strong)_70%,#000)]">
                            <img src="{{ asset('images/fifa-world-cup-2026.svg') }}" alt="FIFA World Cup 2026" class="h-full w-full object-contain dark:brightness-0 dark:invert" loading="eager" decoding="async">
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate font-display text-sm font-black leading-tight text-[var(--app-text)] md:text-lg">Quiniela Mundial</span>
                            <span class="hidden text-xs font-extrabold uppercase text-[var(--app-muted)] md:block">#SOMOS26</span>
                        </span>
                    </a>

                    <nav class="hidden flex-wrap items-center gap-2 text-sm lg:flex">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary {{ request()->routeIs('dashboard') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Mesa</a>
                        @unless (auth()->user()->is_admin)
                            <a href="{{ route('pronosticos.edit') }}" class="btn btn-secondary {{ request()->routeIs('pronosticos.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Predicciones</a>
                        @endunless
                        <a href="{{ route('resultados.index') }}" class="btn btn-secondary {{ request()->routeIs('resultados.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Resultados</a>
                        <a href="{{ route('rankings.index') }}" class="btn btn-secondary {{ request()->routeIs('rankings.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Ranking</a>
                        <a href="{{ route('reglas.index') }}" class="btn btn-secondary {{ request()->routeIs('reglas.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Reglas</a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary {{ request()->routeIs('admin.dashboard', 'admin.resultados.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Admin</a>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary {{ request()->routeIs('admin.usuarios.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)]' : '' }}">Usuarios</a>
                        @endif
                    </nav>

                    <div class="flex min-w-0 items-center gap-1.5 text-xs md:flex-wrap md:gap-2 md:text-sm">
                        <span class="hidden max-w-40 truncate rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] px-3 py-2 font-bold text-[var(--app-muted)] sm:block xl:max-w-56">{{ auth()->user()->name }}</span>
                        <button type="button" data-theme-toggle class="btn btn-secondary hidden lg:inline-flex">Tema</button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary min-h-0 px-2.5 py-1.5 text-xs md:min-h-11 md:px-4 md:py-2.5 md:text-sm">Salir</button>
                        </form>
                    </div>
                </div>
            </header>

            <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-[var(--app-border)] bg-[var(--app-panel)]/94 px-2 pb-[calc(.6rem+env(safe-area-inset-bottom))] pt-2 shadow-[0_-18px_50px_rgba(18,11,36,.14)] backdrop-blur-xl lg:hidden" aria-label="Navegacion principal">
                <div class="mx-auto grid max-w-xl {{ auth()->user()->is_admin ? 'grid-cols-4' : 'grid-cols-5' }} gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary min-h-12 px-2 text-xs {{ request()->routeIs('dashboard') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)] text-[var(--app-primary)]' : '' }}">Mesa</a>
                    @unless (auth()->user()->is_admin)
                        <a href="{{ route('pronosticos.edit') }}" class="btn btn-secondary min-h-12 px-1 text-xs {{ request()->routeIs('pronosticos.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)] text-[var(--app-primary)]' : '' }}">Predicciones</a>
                    @endunless
                    <a href="{{ route('resultados.index') }}" class="btn btn-secondary min-h-12 px-1 text-xs {{ request()->routeIs('resultados.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)] text-[var(--app-primary)]' : '' }}">Resultados</a>
                    <a href="{{ route('rankings.index') }}" class="btn btn-secondary min-h-12 px-2 text-xs {{ request()->routeIs('rankings.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)] text-[var(--app-primary)]' : '' }}">Ranking</a>
                    <a href="{{ route('reglas.index') }}" class="btn btn-secondary min-h-12 px-1 text-xs {{ request()->routeIs('reglas.*') ? 'border-[var(--app-primary)] bg-[var(--app-panel-soft)] text-[var(--app-primary)]' : '' }}">Reglas</a>
                </div>
            </nav>
        @endauth

        <main class="page-fade @auth app-shell pb-28 pt-5 lg:py-8 @else grid min-h-screen place-items-center px-3 py-5 sm:px-4 sm:py-10 @endauth">
            @yield('content')
        </main>
    </body>
</html>
