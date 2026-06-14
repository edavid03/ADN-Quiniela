@extends('layouts.app')

@section('title', 'Predicciones de '.$usuario->name.' | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Perfil de juego</span>
            <h1 class="page-title">Predicciones de {{ $usuario->name }}</h1>
            <p class="page-copy">
                {{ '@'.$usuario->username }} &middot; Solo se muestran partidos cuyo plazo de predicci&oacute;n ya cerr&oacute;.
            </p>
        </div>
        <div class="page-actions">
            <a href="{{ route('rankings.index') }}" class="btn btn-secondary">Volver al ranking</a>
        </div>
    </section>

    <section class="surface overflow-hidden">
        <div class="grid grid-cols-1 items-center gap-3 border-b border-[var(--app-border)] px-4 py-4 sm:grid-cols-[1fr_auto] sm:px-5">
            <div>
                <span class="kicker">Partidos cerrados</span>
                <h2 class="font-display text-xl font-black">Historial visible</h2>
            </div>
            <span class="w-fit rounded-lg bg-[var(--app-panel-soft)] px-3 py-2 text-sm font-extrabold text-[var(--app-muted)]">
                {{ $partidos->count() }} partidos
            </span>
        </div>

        <div class="grid grid-cols-1 gap-3 p-3 sm:grid-cols-2 sm:p-4 xl:grid-cols-3">
            @forelse ($partidos as $partido)
                @php $prediccion = $predicciones->get($partido->id); @endphp

                <article class="grid gap-4 rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] p-4">
                    <div>
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2 text-center text-xs font-extrabold leading-tight">
                            <div class="min-w-0">
                                <span class="flag-chip mx-auto text-base">{!! $partido->local?->flagEmojiHtml() !!}</span>
                                <span class="mt-1 block truncate">{{ $partido->local->name ?? 'Local' }}</span>
                            </div>
                            <span class="versus-badge">vs</span>
                            <div class="min-w-0">
                                <span class="flag-chip mx-auto text-base">{!! $partido->visitante?->flagEmojiHtml() !!}</span>
                                <span class="mt-1 block truncate">{{ $partido->visitante->name ?? 'Visitante' }}</span>
                            </div>
                        </div>
                        <div class="mt-3 text-center text-[11px] font-semibold leading-5 text-[var(--app-muted)]">
                            {{ $partido->fechaCaracas()->format('d/m/Y g:i A') }} hora de Caracas
                            @if ($partido->estadio)
                                <br>{{ $partido->estadio }}
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg border border-[var(--app-border)] bg-[var(--app-panel)] px-3 py-3 text-center">
                        <div class="text-[10px] font-black uppercase tracking-wide text-[var(--app-muted)]">Pron&oacute;stico</div>
                        @if ($prediccion)
                            <div class="mt-2 font-display text-3xl font-black leading-none text-[var(--app-text)]">
                                {{ $prediccion->goles_local }} - {{ $prediccion->goles_visitante }}
                            </div>
                            @if ($prediccion->puntos !== null)
                                <div class="mt-2 text-[10px] font-black uppercase {{ $prediccion->puntos > 0 ? 'text-[var(--app-success)]' : 'text-[var(--app-danger)]' }}">
                                    @if ($prediccion->puntos > 0)
                                        Acert&oacute;
                                    @else
                                        No acert&oacute;
                                    @endif
                                </div>
                                <div class="mt-2 rounded-lg bg-[var(--app-panel-soft)] px-2 py-1.5 text-xs font-black uppercase text-[var(--app-muted)]">
                                    {{ $prediccion->puntos }} {{ $prediccion->puntos === 1 ? 'punto' : 'puntos' }}
                                </div>
                            @else
                                <div class="mt-2 text-[10px] font-black uppercase text-[var(--app-muted)]">Pendiente de resultado</div>
                            @endif
                        @else
                            <div class="mt-2 text-sm font-bold text-[var(--app-muted)]">Sin pron&oacute;stico</div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full px-2 py-4 font-semibold text-[var(--app-muted)]">
                    Todav&iacute;a no hay partidos cerrados para mostrar.
                </div>
            @endforelse
        </div>
    </section>
@endsection
