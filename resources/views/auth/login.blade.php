@extends('layouts.app')

@section('title', 'Entrar | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="relative w-full max-w-6xl overflow-hidden rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] shadow-[0_30px_90px_rgba(18,11,36,.18)]">

        <div class="relative grid min-h-[38rem] lg:grid-cols-[1.05fr_.95fr]">
            <aside class="relative overflow-hidden bg-[var(--fwc-primary)] p-6 text-[var(--fwc-aux-cream)] sm:p-8 lg:p-10">
                <div class="absolute inset-0 opacity-45" style="background-image: radial-gradient(circle at 18% 18%, color-mix(in srgb, var(--fwc-red) 42%, transparent), transparent 12rem);"></div>
                <div class="relative flex h-full flex-col justify-between gap-10">
                    <div>
                        <span class="grid h-28 w-28 place-items-center rounded-lg bg-white p-3 shadow-[0_12px_0_rgba(0,0,0,.22)]">
                            <img src="{{ asset('images/fifa-world-cup-2026.svg') }}" alt="FIFA World Cup 2026" class="h-full w-full object-contain" loading="eager" decoding="async">
                        </span>

                        <div class="mt-8 max-w-xl">
                            <span class="inline-flex rounded-lg border border-white/20 bg-white/10 px-3 py-2 font-display text-xs font-black uppercase text-white">Quiniela privada &middot; #SOMOS26</span>
                            <h1 class="mt-5 font-display text-5xl font-black leading-[1.02] text-white md:text-7xl">Vive cada marcador</h1>
                            <p class="mt-5 max-w-md text-lg font-semibold leading-7 text-white/82">Una mesa de pronosticos con ritmo de torneo: calendario, puntos y ranking para seguir cada fecha del Mundial 2026.</p>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-lg border border-white/15 bg-white/10 p-4">
                            <span class="block font-display text-3xl font-black text-[var(--fwc-red)]">48</span>
                            <span class="text-sm font-extrabold text-white/78">equipos</span>
                        </div>
                        <div class="rounded-lg border border-white/15 bg-white/10 p-4">
                            <span class="block font-display text-3xl font-black text-[var(--fwc-aux-green)]">104</span>
                            <span class="text-sm font-extrabold text-white/78">partidos</span>
                        </div>
                        <div class="rounded-lg border border-white/15 bg-white/10 p-4">
                            <span class="block font-display text-3xl font-black text-[var(--fwc-aux-cream)]">3</span>
                            <span class="text-sm font-extrabold text-white/78">puntos exacto</span>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex items-center p-5 sm:p-8 lg:p-10">
                <section class="surface-strong w-full p-6 sm:p-8">
                    <div class="mb-8">
                       
                        <h2 class="mt-3 font-display text-3xl font-black leading-tight text-[var(--app-text)] sm:text-4xl">Entra a tu tablero</h2>
                        <p class="mt-3 max-w-md font-semibold leading-6 text-[var(--app-muted)]">Carga tus marcadores, consulta el ranking y vuelve cuando quieras a ajustar tus pronosticos.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert border-emerald-200 bg-emerald-50 text-[var(--app-success)] dark:border-emerald-900/50 dark:bg-emerald-950/30">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="grid gap-5">
                        @csrf

                        <label class="grid gap-2 text-sm font-extrabold text-[var(--app-text)]" for="username">
                            Usuario
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username" class="rounded-lg px-4 py-3.5 text-base shadow-[inset_0_-3px_0_color-mix(in_srgb,var(--app-border)_45%,transparent)]" placeholder="tu_usuario">
                        </label>

                        <label class="grid gap-2 text-sm font-extrabold text-[var(--app-text)]" for="password">
                            Contrase&ntilde;a
                            <input id="password" name="password" type="password" required autocomplete="current-password" class="rounded-lg px-4 py-3.5 text-base shadow-[inset_0_-3px_0_color-mix(in_srgb,var(--app-border)_45%,transparent)]" placeholder="••••••••">
                        </label>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-[var(--app-muted)]">
                                <input name="remember" type="checkbox" value="1" class="h-4 w-4 accent-[var(--fwc-red)]">
                                Recordarme
                            </label>
                            <span class="rounded-lg bg-[var(--app-panel-soft)] px-3 py-2 text-xs font-black uppercase text-[var(--app-muted)]">FWC26</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            Entrar
                        </button>

                        <p class="text-center text-sm font-semibold text-[var(--app-muted)]">
                            &iquest;Todav&iacute;a no tienes cuenta?
                            <a href="{{ route('register') }}" class="font-extrabold text-[var(--app-secondary)]">Solicita tu registro</a>
                        </p>
                    </form>
                </section>
            </div>
        </div>
    </section>
@endsection
