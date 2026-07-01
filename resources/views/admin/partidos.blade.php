@extends('layouts.app')

@section('title', 'Admin partidos | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Administraci&oacute;n</span>
            <h1 class="page-title">Partidos de la quiniela</h1>
            <p class="page-copy">Crea y edita solo partidos futuros.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.resultados.edit') }}" class="btn btn-secondary">Gestionar resultados</a>
            <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Ver auditor&iacute;a</a>
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
            <span class="kicker">Calendario</span>
            <h2 class="mt-3 font-display text-2xl font-black">Crear partido futuro</h2>
            <p class="mt-2 text-sm font-semibold leading-6 text-[var(--app-muted)]"></p>

            <form method="POST" action="{{ route('admin.partidos.store') }}" class="match-admin-form mt-5">
                @csrf

                <label>
                    <span>Local</span>
                    <select name="local_id" required>
                        <option value="">Seleccionar equipo</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" @selected((string) old('local_id') === (string) $equipo->id)>Grupo {{ $equipo->grupo }} - {{ $equipo->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Visitante</span>
                    <select name="visitante_id" required>
                        <option value="">Seleccionar equipo</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" @selected((string) old('visitante_id') === (string) $equipo->id)>Grupo {{ $equipo->grupo }} - {{ $equipo->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Fecha y hora Venezuela</span>
                    <input name="fecha_caracas" type="datetime-local" min="{{ $minimumCaracasDateTime }}" value="{{ old('fecha_caracas') }}" required>
                </label>

                <label>
                    <span>Fase</span>
                    <input name="fase" type="text" maxlength="30" value="{{ old('fase', 'Grupos') }}" required>
                </label>

                <label class="sm:col-span-2">
                    <span>Estadio</span>
                    <input name="estadio" type="text" maxlength="255" value="{{ old('estadio') }}" placeholder="Opcional">
                </label>

                <button type="submit" class="btn btn-primary sm:col-span-2">Crear partido</button>
            </form>
        </section>

        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[var(--app-border)] px-5 py-4">
                <div>
                    <span class="kicker">Programaci&oacute;n</span>
                    <h2 class="mt-2 font-display text-2xl font-black">Partidos cargados</h2>
                </div>
                <strong class="grid h-11 min-w-11 place-items-center rounded-lg bg-[var(--app-accent)] px-3 font-display text-xl font-black text-[#170f2f]">{{ $partidos->count() }}</strong>
            </div>

            @forelse ($partidos as $partido)
                @php
                    $fechaCaracasValue = $partido->fechaCaracas()->format('Y-m-d\TH:i');
                @endphp

                <article class="border-b border-[var(--app-border)] px-4 py-4 last:border-b-0 sm:px-5">
                    <div class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-start">
                        <div class="min-w-0">
                            <div class="team-versus">
                                <div class="team-versus-side">
                                    <span class="flag-chip">{!! $partido->local?->flagEmojiHtml() !!}</span>
                                    <span class="team-versus-name">{{ $partido->local->name ?? 'Local' }}</span>
                                </div>
                                <span class="versus-badge">vs</span>
                                <div class="team-versus-side">
                                    <span class="team-versus-name">{{ $partido->visitante->name ?? 'Visitante' }}</span>
                                    <span class="flag-chip">{!! $partido->visitante?->flagEmojiHtml() !!}</span>
                                </div>
                            </div>
                            <div class="mt-2 text-sm font-semibold leading-6 text-[var(--app-muted)]">
                                {{ $partido->fechaCaracas()->format('d/m/Y g:i A') }} Caracas
                                <span class="mx-1">&middot;</span>
                                {{ $partido->fecha_utc->copy()->utc()->format('d/m/Y H:i') }} UTC
                                @if ($partido->estadio)
                                    <span class="mx-1">&middot;</span>{{ $partido->estadio }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <details class="match-admin-editor mt-4">
                        <summary>
                            <span class="match-admin-badge is-open">Editable</span>
                            <span class="match-admin-summary-copy">Abrir edici&oacute;n</span>
                        </summary>

                        <form method="POST" action="{{ route('admin.partidos.update', $partido) }}" class="match-admin-form mt-4">
                            @csrf
                            @method('PATCH')

                            <label>
                                <span>Local</span>
                                <select name="local_id" required>
                                    @foreach ($equipos as $equipo)
                                        <option value="{{ $equipo->id }}" @selected((int) old("partidos.{$partido->id}.local_id", $partido->local_id) === (int) $equipo->id)>Grupo {{ $equipo->grupo }} - {{ $equipo->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span>Visitante</span>
                                <select name="visitante_id" required>
                                    @foreach ($equipos as $equipo)
                                        <option value="{{ $equipo->id }}" @selected((int) old("partidos.{$partido->id}.visitante_id", $partido->visitante_id) === (int) $equipo->id)>Grupo {{ $equipo->grupo }} - {{ $equipo->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span>Fecha y hora Caracas</span>
                                <input name="fecha_caracas" type="datetime-local" min="{{ $minimumCaracasDateTime }}" value="{{ old("partidos.{$partido->id}.fecha_caracas", $fechaCaracasValue) }}" required>
                            </label>

                            <label>
                                <span>Fase</span>
                                <input name="fase" type="text" maxlength="30" value="{{ old("partidos.{$partido->id}.fase", $partido->fase) }}" required>
                            </label>

                            <label class="sm:col-span-2">
                                <span>Estadio</span>
                                <input name="estadio" type="text" maxlength="255" value="{{ old("partidos.{$partido->id}.estadio", $partido->estadio) }}" placeholder="Opcional">
                            </label>

                            <button type="submit" class="btn btn-primary sm:col-span-2">Guardar cambios</button>
                        </form>
                    </details>
                </article>
            @empty
                <p class="px-5 py-6 font-semibold text-[var(--app-muted)]">No hay partidos futuros sin pron&oacute;sticos disponibles para editar.</p>
            @endforelse
        </section>
    </div>
@endsection
