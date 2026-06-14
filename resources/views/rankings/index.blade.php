@extends('layouts.app')

@section('title', 'Ranking | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Clasificaci&oacute;n</span>
            <h1 class="page-title">Ranking</h1>
            <p class="page-copy">Tabla de posiciones del grupo seg&uacute;n los puntos acumulados por los pron&oacute;sticos evaluados.</p>
        </div>
        <div class="page-actions"><a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a></div>
    </section>

    <section class="surface overflow-hidden">
        <div class="grid grid-cols-[3rem_1fr_4.5rem] gap-2 bg-[var(--app-panel-soft)] px-4 py-3 text-xs font-extrabold uppercase text-[var(--app-muted)] sm:grid-cols-[4rem_1fr_5rem] sm:gap-3 sm:px-5 sm:text-sm lg:grid-cols-[4rem_1fr_repeat(4,minmax(5.5rem,auto))]">
            <div>#</div>
            <div>Usuario</div>
            <div class="text-right">Puntos</div>
            <div class="hidden text-right lg:block">Exactos</div>
            <div class="hidden text-right lg:block">Evaluados</div>
            <div class="hidden text-right lg:block">Pron&oacute;sticos</div>
        </div>

        @forelse ($rankings as $index => $ranking)
            <article class="grid grid-cols-[3rem_1fr_4.5rem] gap-2 border-t border-[var(--app-border)] px-4 py-4 sm:grid-cols-[4rem_1fr_5rem] sm:gap-3 sm:px-5 lg:grid-cols-[4rem_1fr_repeat(4,minmax(5.5rem,auto))]">
                <div>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-[var(--app-primary)] font-extrabold text-white dark:text-[var(--app-bg)]">{{ $index + 1 }}</span>
                </div>
                <div class="min-w-0">
                    <a
                        href="{{ route('rankings.predicciones.show', $ranking->id) }}"
                        class="group block w-fit max-w-full rounded-sm no-underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[var(--app-primary)]"
                        aria-label="Ver predicciones de {{ $ranking->name }}"
                    >
                        <strong class="block truncate text-[var(--app-text)] transition-colors group-hover:text-[var(--app-primary)]">{{ $ranking->name }}</strong>
                        <span class="block truncate text-sm text-[var(--app-muted)] transition-colors group-hover:text-[var(--app-primary)]">{{ '@'.$ranking->username }}</span>
                    </a>
                    <span class="mt-2 flex flex-wrap gap-1.5 text-[.65rem] font-extrabold uppercase text-[var(--app-muted)] lg:hidden">
                        <span class="rounded bg-[var(--app-panel-soft)] px-2 py-1">{{ (int) $ranking->exactos }} exactos</span>
                        <span class="rounded bg-[var(--app-panel-soft)] px-2 py-1">{{ (int) $ranking->pronosticos }} jugados</span>
                    </span>
                </div>
                <div class="text-right font-display text-xl font-black">{{ (int) $ranking->total_puntos }}</div>
                <div class="hidden text-right font-bold text-[var(--app-muted)] lg:block">{{ (int) $ranking->exactos }}</div>
                <div class="hidden text-right font-bold text-[var(--app-muted)] lg:block">{{ (int) $ranking->evaluados }}</div>
                <div class="hidden text-right font-bold text-[var(--app-muted)] lg:block">{{ (int) $ranking->pronosticos }}</div>
            </article>
        @empty
            <div class="px-5 py-6 text-[var(--app-muted)]">Todav&iacute;a no hay usuarios para mostrar.</div>
        @endforelse
    </section>
@endsection
