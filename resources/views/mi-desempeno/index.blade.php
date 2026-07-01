@extends('layouts.app')

@section('title', 'Mi desempeno | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Estadisticas</span>
            <h1 class="page-title">Mi desempe&ntilde;o</h1>
            <p class="page-copy">Tu resumen en la quiniela: puntos, aciertos y posicion en el ranking.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('resultados.index') }}" class="btn btn-secondary">Ver resultados</a>
            <a href="{{ route('rankings.index') }}" class="btn btn-primary">Ver ranking</a>
        </div>
    </section>

    @if ($evaluados === 0)
        <section class="surface px-5 py-10 text-center">
            <p class="font-display text-lg font-black text-[var(--app-text)]">Todavia no tienes pronosticos evaluados</p>
            <p class="mt-2 text-sm font-semibold leading-6 text-[var(--app-muted)]">Carga tus marcadores y, cuando el admin registre los resultados, veras aqui tu rendimiento.</p>
            <a href="{{ route('pronosticos.edit') }}" class="btn btn-primary mt-6">Cargar pronosticos</a>
        </section>
    @else
        <section class="mb-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="stat-tile" data-mark="1">
                <span class="text-sm font-black uppercase text-[var(--app-muted)]">Puntos totales</span>
                <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $puntos }}</strong>
            </article>
            <article class="stat-tile" data-mark="2">
                <span class="text-sm font-black uppercase text-[var(--app-muted)]">Posicion</span>
                <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $posicion ? '#'.$posicion : '-' }}</strong>
                <span class="relative z-10 mt-1 block text-xs font-bold text-[var(--app-muted)]">de {{ $totalJugadores }}</span>
            </article>
            <article class="stat-tile" data-mark="3">
                <span class="text-sm font-black uppercase text-[var(--app-muted)]">Efectividad</span>
                <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $efectividad }}%</strong>
                <span class="relative z-10 mt-1 block text-xs font-bold text-[var(--app-muted)]">{{ $aciertos }} de {{ $evaluados }} evaluados</span>
            </article>
            <article class="stat-tile" data-mark="4">
                <span class="text-sm font-black uppercase text-[var(--app-muted)]">Pronosticos</span>
                <strong class="relative z-10 mt-3 block font-display text-5xl font-black text-[var(--app-text)]">{{ $pronosticos }}</strong>
                <span class="relative z-10 mt-1 block text-xs font-bold text-[var(--app-muted)]">{{ $evaluados }} evaluados</span>
            </article>
        </section>

        <section class="surface overflow-hidden">
            <div class="border-b border-[var(--app-border)] px-5 py-3">
                <span class="kicker">Desglose de aciertos</span>
                <h2 class="font-display text-lg font-black">Como sumaste tus puntos</h2>
            </div>
            <div class="divide-y divide-[var(--app-border)] px-5">
                @foreach ([['3', 'Marcadores exactos', $exactos, 'Goles de ambos equipos.'], ['1', 'Resultados acertados', $signos, 'Solo el ganador, sin marcador exacto.']] as [$pts, $title, $cantidad, $copy])
                    <div class="grid grid-cols-[2.25rem_1fr_auto] items-center gap-3 py-4">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-[var(--app-primary)] font-display text-sm font-black text-white dark:text-[#170f2f]">{{ $pts }}</span>
                        <div>
                            <strong class="block font-display text-sm">{{ $title }}</strong>
                            <span class="text-xs font-semibold text-[var(--app-muted)]">{{ $copy }}</span>
                        </div>
                        <span class="font-display text-2xl font-black text-[var(--app-text)]">{{ $cantidad }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
