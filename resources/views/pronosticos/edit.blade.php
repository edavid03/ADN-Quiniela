@extends('layouts.app')

@section('title', 'Pronosticos | '.config('app.name', 'Quiniela'))

@section('content')
    <form method="POST" action="{{ route('pronosticos.update') }}">
        @csrf

        <section class="page-header">
            <div>
                <span class="kicker">Tu jugada</span>
                <h1 class="page-title">Mis pron&oacute;sticos</h1>
                <p class="page-copy">Completa o cambia los marcadores. Si ya hab&iacute;as cargado un partido, al guardar se actualiza.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
                @if ($partidos->isNotEmpty())
                    <button data-pronosticos-submit class="btn btn-primary" type="submit">Guardar cambios</button>
                @endif
            </div>
        </section>

        <div class="mb-5 rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] px-4 py-3 font-bold leading-6 text-[var(--app-text)]">
            El resultado a evaluar corresponde a los 90 minutos reglamentarios. No cuenta la pr&oacute;rroga ni la tanda de penaltis.
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-bold text-[var(--app-success)] dark:border-emerald-900/50 dark:bg-emerald-950/30">{{ session('status') }}</div>
        @endif

        @if (session('security_alert'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-bold text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ session('security_alert') }}</div>
        @elseif ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-bold text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ $errors->first() }}</div>
        @endif

        <section class="overflow-hidden rounded-xl border border-[var(--app-border)] bg-[var(--app-panel)]">
            @foreach ($partidos as $partido)
                @php $prediccion = $predicciones->get($partido->id); @endphp
                <article
                    class="grid gap-4 border-b border-[var(--app-border)] px-4 py-4 last:border-b-0 sm:px-5 lg:grid-cols-[1fr_auto] lg:items-center"
                    data-pronostico-partido
                    data-deadline="{{ $partido->fechaLimitePronosticoUtc()->toIso8601String() }}"
                >
                    <div>
                        <div class="team-versus pronostico-versus">
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
                        <div class="mt-2 text-sm leading-6 text-[var(--app-muted)]">
                            {{ $partido->fechaCaracas()->format('d/m/Y g:i A') }}
                            @if ($partido->estadio)
                                &middot; {{ $partido->estadio }}
                            @endif
                        </div>
                    </div>

                    <div class="score-control">
                        <input aria-label="Goles de {{ $partido->local->name ?? 'Local' }}" name="predicciones[{{ $partido->id }}][goles_local]" type="number" min="0" max="99" value="{{ old("predicciones.{$partido->id}.goles_local", $prediccion->goles_local ?? '') }}">
                        <span class="text-center font-extrabold text-[var(--app-muted)]">-</span>
                        <input aria-label="Goles de {{ $partido->visitante->name ?? 'Visitante' }}" name="predicciones[{{ $partido->id }}][goles_visitante]" type="number" min="0" max="99" value="{{ old("predicciones.{$partido->id}.goles_visitante", $prediccion->goles_visitante ?? '') }}">
                    </div>
                </article>
            @endforeach
            <div data-pronosticos-empty class="{{ $partidos->isNotEmpty() ? 'hidden' : '' }} px-5 py-6 text-[var(--app-muted)]">
                No hay partidos disponibles para pronosticar.
            </div>
        </section>
    </form>

    @if ($partidos->isNotEmpty())
        <script>
            (() => {
                const rows = [...document.querySelectorAll('[data-pronostico-partido]')];
                const submit = document.querySelector('[data-pronosticos-submit]');
                const empty = document.querySelector('[data-pronosticos-empty]');

                const removeClosedMatches = () => {
                    rows.forEach((row) => {
                        const deadline = new Date(row.dataset.deadline).getTime();

                        if (! Number.isNaN(deadline) && Date.now() >= deadline) {
                            row.remove();
                        }
                    });

                    const hasOpenMatches = document.querySelector('[data-pronostico-partido]') !== null;
                    submit?.classList.toggle('hidden', ! hasOpenMatches);
                    empty?.classList.toggle('hidden', hasOpenMatches);
                };

                removeClosedMatches();
                window.setInterval(removeClosedMatches, 1000);
            })();
        </script>
    @endif
@endsection
