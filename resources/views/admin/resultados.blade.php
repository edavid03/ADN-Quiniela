@extends('layouts.app')

@section('title', 'Admin resultados | '.config('app.name', 'Quiniela'))

@section('content')
    <form method="POST" action="{{ route('admin.resultados.update') }}">
        @csrf

        <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-4xl font-extrabold tracking-normal">Resultados de partidos</h1>
                <p class="mt-2 max-w-2xl leading-7 text-[var(--app-muted)]">Solo el administrador puede cargar o actualizar resultados. Al guardar, se recalculan los puntos de los pron&oacute;sticos.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] px-4 py-2.5 font-bold text-[var(--app-primary)] no-underline hover:border-[var(--app-primary)]">Volver</a>
                @if ($partidos->isNotEmpty())
                    <button class="rounded-lg bg-[var(--app-primary)] px-4 py-2.5 font-extrabold text-white hover:bg-[var(--app-primary-strong)] dark:text-[var(--app-bg)]" type="submit">Guardar resultados</button>
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
                <article class="grid gap-4 border-b border-[var(--app-border)] px-5 py-4 last:border-b-0 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div class="flex min-w-0 flex-wrap items-center gap-2 font-bold">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--app-panel-soft)]">{!! $partido->local?->flagEmojiHtml() !!}</span>
                            <span>{{ $partido->local->name ?? 'Local' }}</span>
                            <span class="text-sm text-[var(--app-muted)]">vs</span>
                            <span>{{ $partido->visitante->name ?? 'Visitante' }}</span>
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--app-panel-soft)]">{!! $partido->visitante?->flagEmojiHtml() !!}</span>
                        </div>
                        <div class="mt-2 text-sm leading-6 text-[var(--app-muted)]">
                            {{ \Carbon\Carbon::parse($partido->fecha_utc)->format('d/m/Y H:i') }} UTC
                            @if ($partido->estadio)
                                · {{ $partido->estadio }}
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-[minmax(0,5rem)_1rem_minmax(0,5rem)] items-center gap-2">
                        <input name="resultados[{{ $partido->id }}][goles_local]" type="number" min="0" max="99" value="{{ old("resultados.{$partido->id}.goles_local", $partido->goles_local) }}" class="w-full rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] px-3 py-2.5 text-center text-[var(--app-text)] outline-none focus:border-[var(--app-primary)] focus:ring-4 focus:ring-[var(--app-ring)]">
                        <span class="text-center font-extrabold text-[var(--app-muted)]">-</span>
                        <input name="resultados[{{ $partido->id }}][goles_visitante]" type="number" min="0" max="99" value="{{ old("resultados.{$partido->id}.goles_visitante", $partido->goles_visitante) }}" class="w-full rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] px-3 py-2.5 text-center text-[var(--app-text)] outline-none focus:border-[var(--app-primary)] focus:ring-4 focus:ring-[var(--app-ring)]">
                    </div>
                </article>
            @empty
                <div class="px-5 py-6 text-[var(--app-muted)]">Todav&iacute;a no hay partidos cargados.</div>
            @endforelse
        </section>
    </form>
@endsection
