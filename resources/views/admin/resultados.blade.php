@extends('layouts.app')

@section('title', 'Admin resultados | '.config('app.name', 'Quiniela'))

@section('content')
    <form method="POST" action="{{ route('admin.resultados.update') }}">
        @csrf

        <section class="page-header">
            <div>
                <span class="kicker">Administraci&oacute;n</span>
                <h1 class="page-title">Resultados de partidos</h1>
                <p class="page-copy">Solo el administrador puede cargar o actualizar resultados. Al guardar, se recalculan los puntos de los pron&oacute;sticos.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Ver auditor&iacute;a</a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
                @if ($partidos->isNotEmpty())
                    <button class="btn btn-primary" type="submit">Guardar resultados</button>
                @endif
            </div>
        </section>

        @if (session('status'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-bold text-[var(--app-success)] dark:border-emerald-900/50 dark:bg-emerald-950/30">{{ session('status') }}</div>
        @endif

        @if (session('security_alert'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-bold text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ session('security_alert') }}</div>
        @elseif ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-bold text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ $errors->first() }}</div>
        @endif

        <section class="overflow-hidden rounded-xl border border-[var(--app-border)] bg-[var(--app-panel)]">
            @forelse ($partidos as $partido)
                <article class="grid gap-4 border-b border-[var(--app-border)] px-4 py-4 last:border-b-0 sm:px-5 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div class="team-versus">
                            <div class="team-versus-side"><span class="flag-chip">{!! $partido->local?->flagEmojiHtml() !!}</span><span class="team-versus-name">{{ $partido->local->name ?? 'Local' }}</span></div>
                            <span class="versus-badge">vs</span>
                            <div class="team-versus-side"><span class="team-versus-name">{{ $partido->visitante->name ?? 'Visitante' }}</span><span class="flag-chip">{!! $partido->visitante?->flagEmojiHtml() !!}</span></div>
                        </div>
                        <div class="mt-2 text-sm leading-6 text-[var(--app-muted)]">
                            {{ $partido->fechaCaracas()->format('d/m/Y g:i A') }} hora de Caracas
                            @if ($partido->estadio)
                                · {{ $partido->estadio }}
                            @endif
                        </div>
                    </div>

                    <div class="score-control">
                        <input aria-label="Goles de {{ $partido->local->name ?? 'Local' }}" name="resultados[{{ $partido->id }}][goles_local]" type="number" min="0" max="99" value="{{ old("resultados.{$partido->id}.goles_local", $partido->goles_local) }}">
                        <span class="text-center font-extrabold text-[var(--app-muted)]">-</span>
                        <input aria-label="Goles de {{ $partido->visitante->name ?? 'Visitante' }}" name="resultados[{{ $partido->id }}][goles_visitante]" type="number" min="0" max="99" value="{{ old("resultados.{$partido->id}.goles_visitante", $partido->goles_visitante) }}">
                    </div>
                </article>
            @empty
                <div class="px-5 py-6 text-[var(--app-muted)]">Todav&iacute;a no hay partidos cargados.</div>
            @endforelse
        </section>
    </form>
@endsection
