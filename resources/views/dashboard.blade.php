@extends('layouts.app')

@section('title', 'Dashboard | '.config('app.name', 'Quiniela'))

@section('content')
    @if (session('security_alert'))
        <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">
            {{ session('security_alert') }}
        </div>
    @endif

    <section class="mb-6 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
        <div>
            <span class="kicker">FWC26 Quiniela</span>
            <h1 class="mt-3 font-display text-4xl font-black leading-tight text-[var(--app-text)] md:text-6xl">Mesa de la quiniela</h1>
            <p class="mt-3 max-w-2xl text-lg font-semibold leading-7 text-[var(--app-muted)]">Pronosticos, partidos y ranking del grupo con una identidad inspirada en Monterrey 2026.</p>
        </div>
        <div class="flex flex-wrap gap-3 lg:justify-end">
            @unless (auth()->user()->is_admin)
                <a class="btn btn-primary" href="{{ route('pronosticos.edit') }}">Crear o editar pronosticos</a>
            @endunless
            <a class="btn btn-secondary" href="{{ route('rankings.index') }}">Ver ranking</a>
            @if (auth()->user()->is_admin)
                <a class="btn btn-secondary" href="{{ route('admin.dashboard') }}">Dashboard admin</a>
                <a class="btn btn-secondary" href="{{ route('admin.usuarios.index') }}">Gestionar usuarios</a>
            @endif
        </div>
    </section>

    <section class="mb-6 grid gap-4 {{ auth()->user()->is_admin ? 'md:grid-cols-2' : 'md:grid-cols-3' }}">
        <article class="stat-tile" data-mark="48">
            <span class="text-sm font-black uppercase text-[var(--app-muted)]">Equipos</span>
            <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $teamCount }}</strong>
        </article>
        <article class="stat-tile" data-mark="26">
            <span class="text-sm font-black uppercase text-[var(--app-muted)]">Partidos</span>
            <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $matchCount }}</strong>
        </article>
        @unless (auth()->user()->is_admin)
            <article class="stat-tile" data-mark="3">
                <span class="text-sm font-black uppercase text-[var(--app-muted)]">Mis pronosticos</span>
                <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $predictionCount }}</strong>
            </article>
        @endunless
    </section>


    <div class="grid gap-5 lg:grid-cols-[1.35fr_.65fr]">
        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[var(--app-border)] px-5 py-4">
                <div>
                    <span class="kicker">Calendario</span>
                    <h2 class="font-display text-xl font-black">Proximos partidos</h2>
                </div>
                <span class="rounded-lg bg-[var(--app-panel-soft)] px-3 py-2 text-sm font-extrabold text-[var(--app-muted)]">{{ auth()->user()->username ?? 'usuario' }}</span>
            </div>

            @forelse ($nextMatches as $match)
                <article class="match-row lg:grid-cols-[1fr_auto] lg:items-center">
                    <div class="flex min-w-0 flex-wrap items-center gap-2 font-extrabold">
                        <span class="flag-chip">{!! $match->local?->flagEmojiHtml() !!}</span>
                        <span>{{ $match->local->name ?? 'Local' }}</span>
                        <span class="rounded-full bg-[var(--app-secondary)] px-2 py-1 text-xs font-black text-white">vs</span>
                        <span>{{ $match->visitante->name ?? 'Visitante' }}</span>
                        <span class="flag-chip">{!! $match->visitante?->flagEmojiHtml() !!}</span>
                    </div>
                    <div class="text-sm font-semibold leading-6 text-[var(--app-muted)] lg:text-right">
                        {{ \Carbon\Carbon::parse($match->fecha_utc)->format('d/m/Y H:i') }} UTC<br>
                        {{ $match->estadio }}
                    </div>
                </article>
            @empty
                <div class="px-5 py-6 font-semibold text-[var(--app-muted)]">Todavia no hay partidos cargados.</div>
            @endforelse
        </section>

        <aside class="grid gap-5">
            @unless (auth()->user()->is_admin)
                {{-- INICIO CONTADOR REGRESIVO PRONOSTICOS: puedes editar o eliminar esta card completa. --}}
                <article class="stat-tile" data-mark="7">
                    <span class="text-sm font-black uppercase text-[var(--app-muted)]">Cierre de pronosticos</span>

                    @if ($predictionDeadline)
                        <div
                            class="relative z-10 mt-3"
                            data-prediction-countdown
                            data-deadline="{{ $predictionDeadline->copy()->utc()->toIso8601String() }}"
                        >
                            <div style="display: flex; width: 100%; overflow: hidden; border: 1px solid var(--app-border); border-radius: .5rem; background: var(--app-panel-soft);">
                                <div style="flex: 1 1 0; min-width: 0; border-right: 1px solid var(--app-border); padding: .5rem .35rem; text-align: center;">
                                    <strong class="font-display text-[var(--app-text)]" style="display: block; font-size: 1.35rem; font-weight: 900; line-height: 1;" data-countdown-days>--</strong>
                                    <span style="display: block; margin-top: .2rem; overflow: hidden; text-overflow: ellipsis; font-size: .62rem; font-weight: 900; text-transform: uppercase; color: var(--app-muted);">Dias</span>
                                </div>
                                <div style="flex: 1 1 0; min-width: 0; border-right: 1px solid var(--app-border); padding: .5rem .35rem; text-align: center;">
                                    <strong class="font-display text-[var(--app-text)]" style="display: block; font-size: 1.35rem; font-weight: 900; line-height: 1;" data-countdown-hours>--</strong>
                                    <span style="display: block; margin-top: .2rem; overflow: hidden; text-overflow: ellipsis; font-size: .62rem; font-weight: 900; text-transform: uppercase; color: var(--app-muted);">Horas</span>
                                </div>
                                <div style="flex: 1 1 0; min-width: 0; border-right: 1px solid var(--app-border); padding: .5rem .35rem; text-align: center;">
                                    <strong class="font-display text-[var(--app-text)]" style="display: block; font-size: 1.35rem; font-weight: 900; line-height: 1;" data-countdown-minutes>--</strong>
                                    <span style="display: block; margin-top: .2rem; overflow: hidden; text-overflow: ellipsis; font-size: .62rem; font-weight: 900; text-transform: uppercase; color: var(--app-muted);">Minutos</span>
                                </div>
                                <div style="flex: 1 1 0; min-width: 0; padding: .5rem .35rem; text-align: center;">
                                    <strong class="font-display text-[var(--app-text)]" style="display: block; font-size: 1.35rem; font-weight: 900; line-height: 1;" data-countdown-seconds>--</strong>
                                    <span style="display: block; margin-top: .2rem; overflow: hidden; text-overflow: ellipsis; font-size: .62rem; font-weight: 900; text-transform: uppercase; color: var(--app-muted);">Segundos</span>
                                </div>
                            </div>
                            <p class="mt-2 text-xs font-extrabold text-[var(--app-secondary)]" data-countdown-status>Pronosticos abiertos</p>
                        </div>
                    @else
                        <p class="relative z-10 mt-3 text-sm font-semibold leading-6 text-[var(--app-muted)]">Todavia no hay partidos cargados para calcular el cierre.</p>
                    @endif
                </article>
                {{-- FIN CONTADOR REGRESIVO PRONOSTICOS --}}
            @endunless

            <section class="surface overflow-hidden">
                <div class="border-b border-[var(--app-border)] px-5 py-3">
                    <span class="kicker">Reglas</span>
                    <h2 class="font-display text-lg font-black">Puntos</h2>
                </div>
                <div class="divide-y divide-[var(--app-border)] px-5">
                    @foreach ([['3', 'Marcador exacto', 'Goles de ambos equipos.'], ['1', 'Resultado correcto', 'Ganador.'], ['0', 'Sin acierto', 'Resultado distinto.']] as [$points, $title, $copy])
                        <div class="grid grid-cols-[2.25rem_1fr] gap-3 py-3">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[var(--app-primary)] font-display text-sm font-black text-white dark:text-[#170f2f]">{{ $points }}</span>
                            <div>
                                <strong class="block font-display text-sm">{{ $title }}</strong>
                                <span class="text-xs font-semibold text-[var(--app-muted)]">{{ $copy }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    {{-- INICIO SCRIPT CONTADOR REGRESIVO PRONOSTICOS: puedes editar o eliminar este script completo. --}}
    @if (! auth()->user()->is_admin && $predictionDeadline)
        <script>
            (() => {
                const countdown = document.querySelector('[data-prediction-countdown]');

                if (! countdown) {
                    return;
                }

                const deadline = new Date(countdown.dataset.deadline).getTime();

                if (Number.isNaN(deadline)) {
                    return;
                }

                const fields = {
                    days: countdown.querySelector('[data-countdown-days]'),
                    hours: countdown.querySelector('[data-countdown-hours]'),
                    minutes: countdown.querySelector('[data-countdown-minutes]'),
                    seconds: countdown.querySelector('[data-countdown-seconds]'),
                    status: countdown.querySelector('[data-countdown-status]'),
                };

                const twoDigits = (value) => String(value).padStart(2, '0');

                const renderCountdown = () => {
                    const remaining = deadline - Date.now();

                    if (remaining <= 0) {
                        fields.days.textContent = '00';
                        fields.hours.textContent = '00';
                        fields.minutes.textContent = '00';
                        fields.seconds.textContent = '00';
                        fields.status.textContent = 'Pronosticos cerrados';
                        return;
                    }

                    const secondsTotal = Math.floor(remaining / 1000);
                    const days = Math.floor(secondsTotal / 86400);
                    const hours = Math.floor((secondsTotal % 86400) / 3600);
                    const minutes = Math.floor((secondsTotal % 3600) / 60);
                    const seconds = secondsTotal % 60;

                    fields.days.textContent = twoDigits(days);
                    fields.hours.textContent = twoDigits(hours);
                    fields.minutes.textContent = twoDigits(minutes);
                    fields.seconds.textContent = twoDigits(seconds);
                    fields.status.textContent = 'Pronosticos abiertos';
                };

                renderCountdown();
                window.setInterval(renderCountdown, 1000);
            })();
        </script>
    @endif
    {{-- FIN SCRIPT CONTADOR REGRESIVO PRONOSTICOS --}}
@endsection
