@extends('layouts.app')

@section('title', 'Reglas | '.config('app.name', 'Quiniela'))

@section('content')
    <p class="sr-only">Reglas de la quiniela. Marcador exacto: 3 puntos. Resultado correcto: 1 punto. Sin acierto: 0 puntos.</p>

    <section class="rules-hero">
        <div class="rules-hero-copy">
            <span class="kicker rules-hero-kicker">Manual de juego</span>
            <p class="rules-edition">Quiniela Mundial 2026 <span>Reglamento oficial</span></p>
            <h1>Pronostica.<br><em>Suma.</em> Lidera.</h1>
            <p class="rules-hero-intro">Una guía simple para entender cómo jugar, cuándo guardar tus marcadores y cuántos puntos suma cada acierto.</p>
            <div class="rules-hero-actions">
                @unless (auth()->user()->is_admin)
                    <a href="{{ route('pronosticos.edit') }}" class="btn rules-cta">Hacer pronósticos</a>
                @endunless
                <a href="{{ route('dashboard') }}" class="btn rules-ghost">Volver a la mesa</a>
            </div>
        </div>

        <div class="rules-hero-score" aria-label="Resumen del sistema de puntos">
            <span class="rules-score-label">Así se gana</span>
            <div class="rules-score-main">
                <strong>3</strong>
                <span>puntos</span>
                <p>por acertar el marcador exacto</p>
            </div>
            <div class="rules-score-mini-grid">
                <div>
                    <strong>+1</strong>
                    <span>Resultado correcto</span>
                </div>
                <div>
                    <strong>0</strong>
                    <span>Sin acierto</span>
                </div>
            </div>
        </div>
    </section>

    <section class="rules-steps" aria-labelledby="rules-start-title">
        <div class="rules-section-heading">
            <div>
                <span class="kicker">En tres pasos</span>
                <h2 id="rules-start-title">Jugar es así de fácil</h2>
            </div>
            <p>No necesitas hacer cálculos. El sistema evalúa y suma tus puntos cuando se registra el resultado final.</p>
        </div>

        <div class="rules-steps-grid">
            @foreach ([
                ['01', 'Elige el marcador', 'Pronostica los goles del equipo local y visitante en cada partido disponible.'],
                ['02', 'Guárdalo a tiempo', 'Puedes editarlo hasta 60 minutos antes del inicio oficial del encuentro.'],
                ['03', 'Sigue tu ascenso', 'Después del resultado final, tus puntos aparecen automáticamente en el ranking.'],
            ] as [$number, $title, $copy])
                <article class="rules-step">
                    <span class="rules-step-number">{{ $number }}</span>
                    <div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $copy }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="rules-content-grid">
        <section class="rules-guide" aria-labelledby="rules-guide-title">
            <div class="rules-section-heading rules-guide-heading">
                <div>
                    <span class="kicker">Reglamento</span>
                    <h2 id="rules-guide-title">Lo esencial, sin letra pequeña</h2>
                </div>
            </div>

            <div class="rules-guide-grid">
                @foreach ([
                    ['Cierre de jugadas', 'El reloj manda', 'Los pronósticos cierran 60 minutos antes del inicio. Después de ese momento no pueden crearse ni modificarse.', '60 min', 'antes'],
                    ['Puntuación máxima', 'Marcador exacto', 'Acierta los goles de ambos equipos y recibes la máxima puntuación disponible por ese partido.', '+3', 'puntos'],
                    ['Acierto parcial', 'Ganador o empate', 'Si aciertas quién gana o que el partido termina empatado, pero no el marcador exacto, sumas un punto.', '+1', 'punto'],
                    ['Clasificación', 'Cada punto cuenta', 'La tabla se ordena por la suma total de puntos obtenidos durante toda la competencia.', '#1', 'objetivo'],
                ] as [$eyebrow, $title, $copy, $mark, $unit])
                    <article class="rules-guide-card">
                        <div class="rules-guide-mark">
                            <strong>{{ $mark }}</strong>
                            <span>{{ $unit }}</span>
                        </div>
                        <div>
                            <span class="rules-card-eyebrow">{{ $eyebrow }}</span>
                            <h3>{{ $title }}</h3>
                            <p>{{ $copy }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="rules-example-column">
            <section class="rules-example-card">
                <span class="kicker rules-light-kicker">Ejemplo rápido</span>
                <h2>El resultado final es <span>2 - 1</span></h2>

                <div class="rules-match">
                    <div>
                        <span class="rules-team-dot rules-team-dot-local" aria-hidden="true"></span>
                        <strong>Local</strong>
                    </div>
                    <span class="rules-final-score">2 <small>Final</small> 1</span>
                    <div>
                        <span class="rules-team-dot rules-team-dot-away" aria-hidden="true"></span>
                        <strong>Visitante</strong>
                    </div>
                </div>

                <div class="rules-example-list">
                    <div>
                        <span>Tu pronóstico</span>
                        <strong>2 - 1</strong>
                        <b class="is-exact">+3 pts</b>
                    </div>
                    <div>
                        <span>Tu pronóstico</span>
                        <strong>1 - 0</strong>
                        <b class="is-result">+1 pt</b>
                    </div>
                    <div>
                        <span>Tu pronóstico</span>
                        <strong>0 - 1</strong>
                        <b class="is-miss">0 pts</b>
                    </div>
                </div>
            </section>

            <section class="rules-reminder">
                <span class="rules-reminder-mark">!</span>
                <div>
                    <span class="rules-card-eyebrow">Consejo importante</span>
                    <h2>No esperes al último minuto</h2>
                    <p>Revisa el contador de la mesa y guarda tus marcadores con tiempo. Un partido cerrado desaparece de la vista de pronósticos.</p>
                </div>
            </section>
        </aside>
    </div>
@endsection
