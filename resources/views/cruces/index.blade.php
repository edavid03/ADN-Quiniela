@extends('layouts.app')

@section('title', 'Cruces | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Eliminatorias</span>
            <h1 class="page-title">Cuadro de cruces</h1>
            <p class="page-copy">El camino a la final desde dieciseisavos. El cuadro se completa a medida que se cargan los resultados.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('resultados.index') }}" class="btn btn-secondary">Resultados</a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver a mesa</a>
        </div>
    </section>

    <section class="surface overflow-hidden">
        <div class="grid grid-cols-[1fr_auto] items-center gap-3 border-b border-[var(--app-border)] px-5 py-4">
            <div>
                <span class="kicker">Camino a la final</span>
                <h2 class="font-display text-xl font-black">Fase eliminatoria</h2>
            </div>
            <span class="rounded-lg bg-[var(--app-panel-soft)] px-3 py-2 text-sm font-extrabold text-[var(--app-muted)]">Mundial 2026</span>
        </div>

        <div class="bracket-scroll">
            <div class="bracket">
                <div class="bracket-side bracket-side--left">
                    @foreach ($bracket['left'] as $col)
                        <div class="bracket-col">
                            <div class="bracket-col__head"><span class="kicker">{{ $col['label'] }}</span></div>
                            @foreach ($col['matches'] as $match)
                                <x-bracket-match :match="$match" side="left" />
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="bracket-center">
                    <div class="bracket-col__head"><span class="kicker">Final</span></div>
                    <div class="bracket-trophy" aria-hidden="true">
                        <img src="{{ asset('images/fifa-world-cup-2026.svg') }}" alt="" class="bracket-trophy__img dark:brightness-0 dark:invert">
                    </div>
                    <x-bracket-match :match="$bracket['final']" side="center" class="bracket-cell--final" />

                    <div class="bracket-third">
                        <div class="bracket-col__head bracket-col__head--center"><span class="kicker">Tercer puesto</span></div>
                        <x-bracket-match :match="$bracket['third']" side="center" />
                    </div>
                </div>

                <div class="bracket-side bracket-side--right">
                    @foreach ($bracket['right'] as $col)
                        <div class="bracket-col">
                            <div class="bracket-col__head"><span class="kicker">{{ $col['label'] }}</span></div>
                            @foreach ($col['matches'] as $match)
                                <x-bracket-match :match="$match" side="right" />
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
