@extends('layouts.app')

@section('title', 'Registro | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="relative w-full max-w-6xl overflow-hidden rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] shadow-[0_30px_90px_rgba(18,11,36,.18)]">
        <div class="grid lg:grid-cols-[.78fr_1.22fr]">
            <aside class="relative overflow-hidden bg-[var(--fwc-primary)] p-5 text-white sm:p-9 lg:p-10">
                <div class="absolute inset-0 opacity-50" style="background: radial-gradient(circle at 12% 12%, var(--fwc-red), transparent 12rem), linear-gradient(145deg, transparent 50%, color-mix(in srgb, var(--fwc-accent-2) 55%, transparent) 50% 55%, transparent 55%);"></div>
                <div class="relative flex h-full min-h-0 flex-col justify-between gap-6 sm:min-h-72 sm:gap-10">
                    <div>
                        <span class="kicker text-[var(--fwc-accent)]">Nueva convocatoria</span>
                        <h1 class="mt-4 font-display text-4xl font-black leading-[1.02] sm:mt-5 sm:text-6xl">Entra a la quiniela</h1>
                        <p class="mt-5 max-w-sm text-lg font-semibold leading-7 text-white/80">Crea tu solicitud. El administrador revisar&aacute; tus datos antes de habilitar el acceso.</p>
                    </div>
                    <div class="rounded-lg border border-white/15 bg-white/10 p-4 text-sm font-bold leading-6 text-white/80">
                        La c&eacute;dula identifica cada cuenta de forma &uacute;nica. Tu contrase&ntilde;a debe tener al menos 8 caracteres.
                    </div>
                </div>
            </aside>

            <div class="p-4 sm:p-8 lg:p-10">
                <div class="mb-7 grid gap-4 sm:grid-cols-[1fr_auto] sm:items-start">
                    <div>
                        <span class="kicker">Solicitud de acceso</span>
                        <h2 class="mt-3 font-display text-3xl font-black text-[var(--app-text)]">Registra tus datos</h2>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Volver al login</a>
                </div>

                @if ($errors->any())
                    <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="grid gap-5 sm:grid-cols-2">
                    @csrf

                    <label class="grid gap-2 text-sm font-extrabold" for="name">
                        Nombre completo
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="rounded-lg px-4 py-3">
                    </label>

                    <label class="grid gap-2 text-sm font-extrabold" for="username">
                        Usuario
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" class="rounded-lg px-4 py-3">
                    </label>

                    <label class="grid gap-2 text-sm font-extrabold" for="cedula">
                        C&eacute;dula
                        <input id="cedula" name="cedula" type="text" inputmode="numeric" pattern="[0-9]{6,12}" minlength="6" maxlength="12" value="{{ old('cedula') }}" required autocomplete="off" class="rounded-lg px-4 py-3" placeholder="Solo d&iacute;gitos">
                    </label>

                    <label class="grid gap-2 text-sm font-extrabold" for="email">
                        Correo electr&oacute;nico
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="rounded-lg px-4 py-3">
                    </label>

                    <label class="grid gap-2 text-sm font-extrabold" for="password">
                        Contrase&ntilde;a
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="rounded-lg px-4 py-3">
                    </label>

                    <label class="grid gap-2 text-sm font-extrabold" for="password_confirmation">
                        Confirmar contrase&ntilde;a
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="rounded-lg px-4 py-3">
                    </label>

                    <button type="submit" class="btn btn-primary sm:col-span-2">Enviar solicitud de registro</button>
                </form>
            </div>
        </div>
    </section>
@endsection
