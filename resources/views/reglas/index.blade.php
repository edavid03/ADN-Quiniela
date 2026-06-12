@extends('layouts.app')

@section('title', 'Reglas | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Manual de juego</span>
            <h1 class="page-title">Reglas de la quiniela</h1>
            <p class="page-copy">Todo lo necesario para jugar limpio, sumar puntos y competir por la cima.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver a la mesa</a>
            @unless (auth()->user()->is_admin)
                <a href="{{ route('pronosticos.edit') }}" class="btn btn-primary">Hacer pron&oacute;sticos</a>
            @endunless
        </div>
    </section>

    <section class="rules-scoreboard">
        <div>
            <span class="kicker rules-scoreboard-kicker">Sistema de puntos</span>
            <h2 class="mt-3 font-display text-3xl font-black text-white sm:text-4xl">Cada marcador cuenta</h2>
            <p class="mt-3 max-w-xl text-sm font-semibold leading-7 text-white/70">Los puntos se calculan cuando el administrador registra el resultado final de cada partido.</p>
        </div>
        <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <article class="rules-score">
                <strong>3</strong>
                <span>Exacto</span>
            </article>
            <article class="rules-score">
                <strong>1</strong>
                <span>Resultado</span>
            </article>
            <article class="rules-score rules-score-muted">
                <strong>0</strong>
                <span>Sin acierto</span>
            </article>
        </div>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.25fr_.75fr]">
        <section class="surface overflow-hidden">
            <div class="border-b border-[var(--app-border)] px-5 py-4">
                <span class="kicker">Reglamento oficial</span>
                <h2 class="mt-2 font-display text-2xl font-black">C&oacute;mo funciona</h2>
            </div>

            @foreach ([
                ['01', 'Registra tu marcador', 'Pronostica los goles del equipo local y visitante antes de la hora de cierre de cada partido.'],
                ['02', 'Respeta el cierre', 'Los pron&oacute;sticos cierran 60 minutos antes del inicio del partido. Despu&eacute;s de ese momento no podr&aacute;n crearse ni modificarse.'],
                ['03', 'Marcador exacto: 3 puntos', 'Si aciertas los goles de ambos equipos, recibes tres puntos por ese partido.'],
                ['04', 'Resultado correcto: 1 punto', 'Si aciertas al ganador o el empate, pero no el marcador exacto, recibes un punto.'],
                ['05', 'Sin acierto: 0 puntos', 'Si el ganador o empate pronosticado no coincide con el resultado final, no sumas puntos.'],
                ['06', 'Gana quien lidere el ranking', 'La clasificaci&oacute;n se ordena por la suma total de puntos obtenidos durante la competencia.'],
            ] as [$number, $title, $copy])
                <article class="rule-row">
                    <span class="rule-number">{{ $number }}</span>
                    <div>
                        <h3 class="font-display text-lg font-black sm:text-xl">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm font-semibold leading-7 text-[var(--app-muted)]">{{ $copy }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <aside class="grid content-start gap-5">
            <section class="surface-strong p-5">
                <span class="kicker">Ejemplo r&aacute;pido</span>
                <div class="mt-5 rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-soft)] p-4">
                    <div class="team-versus">
                        <div class="team-versus-side">
                            <span class="flag-chip">🇻🇪</span>
                            <span class="team-versus-name">Local</span>
                        </div>
                        <span class="versus-badge">2 - 1</span>
                        <div class="team-versus-side">
                            <span class="team-versus-name">Visitante</span>
                            <span class="flag-chip">🌎</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid gap-3">
                    <div class="rule-example">
                        <strong>Pron&oacute;stico 2 - 1</strong>
                        <span class="rule-points">+3 pts</span>
                    </div>
                    <div class="rule-example">
                        <strong>Pron&oacute;stico 1 - 0</strong>
                        <span class="rule-points rule-points-secondary">+1 pt</span>
                    </div>
                    <div class="rule-example">
                        <strong>Pron&oacute;stico 0 - 1</strong>
                        <span class="rule-points rule-points-muted">0 pts</span>
                    </div>
                </div>
            </section>

            <section class="surface-strong border-l-4 border-l-[var(--app-secondary)] p-5">
                <span class="font-display text-xs font-black uppercase text-[var(--app-secondary)]">Importante</span>
                <h2 class="mt-2 font-display text-xl font-black">Juega antes del cierre</h2>
                <p class="mt-2 text-sm font-semibold leading-7 text-[var(--app-muted)]">Revisa el contador de la mesa y guarda tus marcadores con tiempo. Un partido cerrado desaparece de la vista de pron&oacute;sticos.</p>
            </section>
        </aside>
    </div>
@endsection
