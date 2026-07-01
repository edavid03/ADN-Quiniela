@extends('layouts.app')

@section('title', 'Pronosticos publicos | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Pronosticos de todos</span>
            <h1 class="page-title">Pronosticos publicos</h1>
            <p class="page-copy">Todos los pronosticos de los jugadores para los partidos ya finalizados.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver a mesa</a>
            <a href="{{ route('rankings.index') }}" class="btn btn-primary">Ver ranking</a>
        </div>
    </section>

    <section class="surface overflow-hidden">
        <div class="grid grid-cols-[1fr_auto] items-center gap-3 border-b border-[var(--app-border)] px-5 py-4">
            <div>
                <span class="kicker">Resultados y apuestas</span>
                <h2 class="font-display text-xl font-black">Partidos finalizados</h2>
            </div>
            <span class="rounded-lg bg-[var(--app-panel-soft)] px-3 py-2 text-sm font-extrabold text-[var(--app-muted)]">{{ $partidos->count() }} partidos</span>
        </div>

        @forelse ($partidos->groupBy('fase') as $fase => $partidosFase)
            <div class="flex items-center justify-between gap-3 border-t border-[var(--app-border)] bg-[var(--app-panel-soft)] px-5 py-3 first:border-t-0">
                <span class="kicker">{{ $fase }}</span>
                <span class="text-xs font-extrabold text-[var(--app-muted)]">{{ $partidosFase->count() }}</span>
            </div>
            <div class="grid grid-cols-1 gap-3 p-3 sm:p-4">
                @foreach ($partidosFase as $partido)
                    @php($prediccionesPorUsuario = $partido->predicciones->keyBy('usuario_id'))

                    <details class="group rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] text-xs [&::-webkit-details-marker]:hidden">
                        <summary class="relative list-none cursor-pointer p-4 transition hover:bg-[var(--app-panel-soft)]">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--app-muted)] transition-transform group-open:rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </div>

                            <div class="grid gap-3 pr-6">
                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2 text-center text-sm font-extrabold leading-tight md:text-base">
                                    <div class="min-w-0">
                                        <span class="flag-chip mx-auto text-3xl md:text-4xl">{!! $partido->local?->flagEmojiHtml() !!}</span>
                                        <span class="mt-2 block truncate">{{ $partido->local->name ?? 'Local' }}</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-[9px] font-black uppercase text-[var(--app-muted)]">Resultado</span>
                                        <div class="mt-1 font-display text-3xl font-black leading-none text-[var(--app-text)] md:text-4xl">
                                            {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="flag-chip mx-auto text-3xl md:text-4xl">{!! $partido->visitante?->flagEmojiHtml() !!}</span>
                                        <span class="mt-2 block truncate">{{ $partido->visitante->name ?? 'Visitante' }}</span>
                                    </div>
                                </div>

                                <div class="text-center text-[10px] font-semibold leading-4 text-[var(--app-muted)]">
                                    <x-local-time :date="$partido->fecha_utc" />
                                    @if ($partido->estadio)
                                        <br>{{ $partido->estadio }}
                                    @endif
                                </div>
                            </div>
                        </summary>

                        <div class="overflow-x-auto border-t border-[var(--app-border)] p-4">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-[var(--app-border)] text-[9px] font-black uppercase text-[var(--app-muted)]">
                                        <th class="px-2 py-1.5">Jugador</th>
                                        <th class="px-2 py-1.5 text-center">Pronostico</th>
                                        <th class="px-2 py-1.5 text-center">Pts</th>
                                        <th class="hidden px-2 py-1.5 text-right sm:table-cell">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $jugadoresOrdenados = $users->sortByDesc(function ($user) use ($prediccionesPorUsuario) {
                                            $pred = $prediccionesPorUsuario->get($user->id);
                                            return (!$pred || $pred->puntos === null) ? -1 : $pred->puntos;
                                        });
                                    @endphp
                                    @forelse ($jugadoresOrdenados as $user)
                                        @php
                                            $pred = $prediccionesPorUsuario->get($user->id);
                                            $tienePred = $pred !== null && $pred->goles_local !== null;
                                            $evaluada = $tienePred && $pred->puntos !== null;
                                            $clase = '';
                                            $estado = '';
                                            if (! $tienePred) {
                                                $clase = 'text-[var(--app-muted)] opacity-50';
                                                $estado = 'Sin pronostico';
                                            } elseif ($pred->puntos === 3) {
                                                $clase = 'bg-green-900/20 text-green-300';
                                                $estado = 'Exacto';
                                            } elseif ($pred->puntos === 1) {
                                                $clase = 'bg-yellow-900/20 text-yellow-300';
                                                $estado = 'Acerto resultado';
                                            } elseif ($pred->puntos === 0) {
                                                $clase = 'bg-red-900/20 text-red-300';
                                                $estado = 'Sin acierto';
                                            }
                                        @endphp
                                        <tr class="{{ $clase }} border-b border-[var(--app-border)] last:border-b-0 transition hover:brightness-110">
                                            <td class="px-2 py-1.5 font-bold">{{ $user->name }}</td>
                                            <td class="px-2 py-1.5 text-center font-display text-base font-black">{{ $tienePred ? $pred->goles_local.' - '.$pred->goles_visitante : '--' }}</td>
                                            <td class="px-2 py-1.5 text-center font-display text-base font-black">{{ $evaluada ? $pred->puntos : '--' }}</td>
                                            <td class="hidden px-2 py-1.5 text-right text-[10px] font-black uppercase sm:table-cell">{{ $tienePred && $evaluada ? $estado : ($tienePred ? 'Por evaluar' : 'Sin pronostico') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-2 py-4 text-center font-semibold text-[var(--app-muted)]">No hay jugadores aprobados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        @empty
            <div class="px-5 py-6 font-semibold text-[var(--app-muted)]">Todavia no hay partidos finalizados con resultados cargados.</div>
        @endforelse
    </section>
@endsection
