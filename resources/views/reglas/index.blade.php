@extends('layouts.app')

@section('title', 'Reglas | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Guía de juego</span>
            <h1 class="page-title">Reglas de la quiniela</h1>
            <p class="page-copy">Pronostica el marcador de cada partido antes del cierre y suma puntos según tus aciertos.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver a la mesa</a>
            @unless (auth()->user()->is_admin)
                <a href="{{ route('pronosticos.edit') }}" class="btn btn-primary">Hacer pronósticos</a>
            @endunless
        </div>
    </section>

    <section class="rules-simple-section" aria-labelledby="scoring-title">
        <div class="rules-simple-heading">
            <span class="rules-simple-step">1</span>
            <div>
                <h2 id="scoring-title">¿Cómo se suman puntos?</h2>
                <p>El sistema calcula los puntos cuando se registra el resultado final.</p>
            </div>
        </div>

        <div class="rules-points-grid">
            <article class="rules-point-card">
                <strong>3</strong>
                <div>
                    <h3>Marcador exacto: 3 puntos</h3>
                    <p>Aciertas los goles de ambos equipos.</p>
                </div>
            </article>
            <article class="rules-point-card">
                <strong>1</strong>
                <div>
                    <h3>Resultado correcto: 1 punto</h3>
                    <p>Aciertas el ganador o el empate, pero no el marcador.</p>
                </div>
            </article>
            <article class="rules-point-card">
                <strong>0</strong>
                <div>
                    <h3>Sin acierto: 0 puntos</h3>
                    <p>El resultado pronosticado no coincide.</p>
                </div>
            </article>
        </div>

        <div class="rules-note">
            <strong>Tiempo reglamentario</strong>
            <p>El resultado evaluado corresponde solo a los 90 minutos reglamentarios. No cuenta la prorroga ni la tanda de penaltis.</p>
        </div>
    </section>

    <div class="rules-simple-layout">
        <section class="rules-simple-section" aria-labelledby="play-title">
            <div class="rules-simple-heading">
                <span class="rules-simple-step">2</span>
                <div>
                    <h2 id="play-title">¿Cómo jugar?</h2>
                    <p>Sigue estos pasos para que tus pronósticos sean válidos.</p>
                </div>
            </div>

            <ol class="rules-list">
                <li>
                    <strong>Registra el marcador</strong>
                    <span>Indica los goles del equipo local y visitante en cada partido disponible.</span>
                </li>
                <li>
                    <strong>Guarda antes del cierre</strong>
                    <span>Puedes crear o editar tu pronóstico hasta 60 minutos antes del inicio del partido.</span>
                </li>
                <li>
                    <strong>Consulta tus puntos</strong>
                    <span>Después del resultado final, tus puntos aparecen automáticamente en el ranking.</span>
                </li>
                <li>
                    <strong>Compite por el primer lugar</strong>
                    <span>La clasificación se ordena por la suma total de puntos obtenidos.</span>
                </li>
            </ol>
        </section>

        <aside class="rules-simple-section" aria-labelledby="example-title">
            <div class="rules-simple-heading">
                <span class="rules-simple-step">3</span>
                <div>
                    <h2 id="example-title">Ejemplo</h2>
                    <p>Si el resultado final es <strong>2 - 1</strong>:</p>
                </div>
            </div>

            <div class="rules-example-table">
                <div class="rules-example-header">
                    <span>Pronóstico</span>
                    <span>Puntos</span>
                </div>
                <div>
                    <strong>2 - 1</strong>
                    <span class="rules-result rules-result-exact">3 puntos</span>
                </div>
                <div>
                    <strong>1 - 0</strong>
                    <span class="rules-result rules-result-partial">1 punto</span>
                </div>
                <div>
                    <strong>0 - 1</strong>
                    <span class="rules-result">0 puntos</span>
                </div>
            </div>

            <div class="rules-note">
                <strong>Importante</strong>
                <p>Guarda tus marcadores con tiempo. Cuando un partido cierra, ya no puede pronosticarse ni editarse.</p>
            </div>
        </aside>
    </div>
@endsection
