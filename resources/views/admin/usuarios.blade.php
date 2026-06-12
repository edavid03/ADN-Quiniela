@extends('layouts.app')

@section('title', 'Gestionar usuarios | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Administraci&oacute;n</span>
            <h1 class="page-title">Gesti&oacute;n de usuarios</h1>
            <p class="page-copy">Acepta solicitudes, crea accesos manuales y elimina cuentas que todav&iacute;a no tengan pron&oacute;sticos.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.resultados.edit') }}" class="btn btn-secondary">Gestionar resultados</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    @if (session('status'))
        <div class="alert border-emerald-200 bg-emerald-50 text-[var(--app-success)] dark:border-emerald-900/50 dark:bg-emerald-950/30">{{ session('status') }}</div>
    @endif

    @if (session('security_alert'))
        <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ session('security_alert') }}</div>
    @elseif ($errors->any())
        <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[.82fr_1.18fr]">
        <section class="surface-strong h-fit p-4 sm:p-5">
            <span class="kicker">Alta directa</span>
            <h2 class="mt-3 font-display text-2xl font-black">Crear usuario aprobado</h2>
            <p class="mt-2 text-sm font-semibold leading-6 text-[var(--app-muted)]">La cuenta podr&aacute; iniciar sesi&oacute;n inmediatamente.</p>

            <form method="POST" action="{{ route('admin.usuarios.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                @csrf

                <label class="grid gap-2 text-sm font-extrabold sm:col-span-2">
                    Nombre completo
                    <input name="name" type="text" value="{{ old('name') }}" required class="rounded-lg px-3 py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-extrabold">
                    Usuario
                    <input name="username" type="text" value="{{ old('username') }}" required class="rounded-lg px-3 py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-extrabold">
                    C&eacute;dula
                    <input name="cedula" type="text" inputmode="numeric" pattern="[0-9]{6,12}" minlength="6" maxlength="12" value="{{ old('cedula') }}" required class="rounded-lg px-3 py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-extrabold sm:col-span-2">
                    Correo electr&oacute;nico
                    <input name="email" type="email" value="{{ old('email') }}" required class="rounded-lg px-3 py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-extrabold">
                    Contrase&ntilde;a
                    <input name="password" type="password" required class="rounded-lg px-3 py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-extrabold">
                    Confirmar contrase&ntilde;a
                    <input name="password_confirmation" type="password" required class="rounded-lg px-3 py-2.5">
                </label>
                <button type="submit" class="btn btn-primary sm:col-span-2">Crear usuario</button>
            </form>
        </section>

        <div class="grid gap-6">
            <section class="surface overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-[var(--app-border)] px-5 py-4">
                    <div>
                        <span class="kicker">Por revisar</span>
                        <h2 class="mt-2 font-display text-2xl font-black">Solicitudes pendientes</h2>
                    </div>
                    <strong class="grid h-11 min-w-11 place-items-center rounded-lg bg-[var(--app-accent)] px-3 font-display text-xl font-black text-[#170f2f]">{{ $pendingUsers->count() }}</strong>
                </div>

                @forelse ($pendingUsers as $user)
                    <article class="grid gap-4 border-b border-[var(--app-border)] px-4 py-4 last:border-b-0 sm:px-5 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="min-w-0">
                            <strong class="block truncate font-display text-lg">{{ $user->name }}</strong>
                            <span class="block break-words text-sm font-semibold leading-6 text-[var(--app-muted)]">{{ '@'.$user->username }} &middot; {{ $user->email }} &middot; C.I. {{ $user->cedula }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 md:flex">
                            <form method="POST" action="{{ route('admin.usuarios.approve', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary min-h-0 py-2">Aceptar</button>
                            </form>
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary min-h-0 py-2 text-[var(--app-danger)]">Eliminar</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="px-5 py-6 font-semibold text-[var(--app-muted)]">No hay solicitudes pendientes.</p>
                @endforelse
            </section>

            <section class="surface overflow-hidden">
                <div class="border-b border-[var(--app-border)] px-5 py-4">
                    <span class="kicker">Directorio</span>
                    <h2 class="mt-2 font-display text-2xl font-black">Usuarios aprobados</h2>
                </div>

                @foreach ($approvedUsers as $user)
                    <article class="grid gap-4 border-b border-[var(--app-border)] px-4 py-4 last:border-b-0 sm:px-5 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <strong class="truncate font-display text-lg">{{ $user->name }}</strong>
                                @if ($user->is_admin)
                                    <span class="rounded-lg bg-[var(--app-primary)] px-2 py-1 text-xs font-black uppercase text-white dark:text-[#170f2f]">Admin</span>
                                @endif
                            </div>
                            <span class="block break-words text-sm font-semibold leading-6 text-[var(--app-muted)]">{{ '@'.$user->username }} &middot; {{ $user->email }} &middot; C.I. {{ $user->cedula ?: 'pendiente' }}</span>
                            <span class="mt-1 block text-xs font-extrabold uppercase text-[var(--app-secondary)]">{{ $user->predicciones_count }} pron&oacute;sticos</span>
                        </div>
                        @unless ($user->is_admin)
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary min-h-0 py-2 text-[var(--app-danger)]">Eliminar</button>
                            </form>
                        @endunless
                    </article>
                @endforeach
            </section>
        </div>
    </div>
@endsection
