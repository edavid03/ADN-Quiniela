@extends('layouts.app')

@section('title', 'Auditoria | '.config('app.name', 'Quiniela'))

@section('content')
    @php
        $actionMeta = [
            'created' => ['label' => 'Creaci&oacute;n', 'tone' => 'audit-badge-create', 'mark' => '+'],
            'updated' => ['label' => 'Actualizaci&oacute;n', 'tone' => 'audit-badge-update', 'mark' => '~'],
            'deleted' => ['label' => 'Eliminaci&oacute;n', 'tone' => 'audit-badge-delete', 'mark' => '-'],
        ];

        $activeFilters = collect([
            $selectedActorIds->isNotEmpty() ? 'Usuarios' : null,
            request('action') ? 'Acci&oacute;n' : null,
            request('table_name') ? 'Tabla' : null,
        ])->filter()->count();
    @endphp

    <section class="audit-hero">
        <div class="audit-hero-copy">
            <span class="kicker">Administraci&oacute;n</span>
            <h1>Historial de auditor&iacute;a</h1>
            <p>Rastrea cada cambio sensible de la quiniela: qui&eacute;n lo hizo, sobre qu&eacute; registro y qu&eacute; valores se movieron.</p>
        </div>
        <div class="audit-hero-panel" aria-label="Resumen de auditoria">
            <span class="audit-hero-label">Eventos encontrados</span>
            <strong>{{ $auditorias->total() }}</strong>
            <div>
                <span>{{ $activeFilters }} filtros activos</span>
                <span>{{ $auditorias->count() }} en esta p&aacute;gina</span>
            </div>
        </div>
    </section>

    <div class="audit-toolbar">
        <form method="GET" action="{{ route('admin.auditoria.index') }}" class="audit-filters">
            <fieldset class="audit-user-filter">
                <legend>
                    <span>Usuarios</span>
                    <small>{{ $selectedActorIds->isNotEmpty() ? $selectedActorIds->count().' seleccionados' : 'Todos los actores' }}</small>
                </legend>

                <div class="audit-user-picker">
                    @foreach ($actors as $actor)
                        <label>
                            <input type="checkbox" name="actor_ids[]" value="{{ $actor->actor_id }}" @checked($selectedActorIds->contains((int) $actor->actor_id))>
                            <span>
                                <strong>{{ $actor->actor_name }}</strong>
                                @if ($actor->is_admin)
                                    <small>Admin</small>
                                @else
                                    <small>Usuario</small>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <label>
                <span>Acci&oacute;n</span>
                <select name="action">
                    <option value="">Todas</option>
                    <option value="created" @selected(request('action') === 'created')>Creaci&oacute;n</option>
                    <option value="updated" @selected(request('action') === 'updated')>Actualizaci&oacute;n</option>
                    <option value="deleted" @selected(request('action') === 'deleted')>Eliminaci&oacute;n</option>
                </select>
            </label>

            <label>
                <span>Tabla</span>
                <select name="table_name">
                    <option value="">Todas</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table }}" @selected(request('table_name') === $table)>{{ $table }}</option>
                    @endforeach
                </select>
            </label>

            <div class="audit-filter-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        <div class="audit-shortcuts">
            <a href="{{ route('admin.resultados.edit') }}" class="btn btn-secondary">Resultados</a>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Usuarios</a>
        </div>
    </div>

    <section class="audit-ledger" aria-label="Eventos de auditoria">
        @forelse ($auditorias as $auditoria)
            @php
                $meta = $actionMeta[$auditoria->action] ?? ['label' => e($auditoria->action), 'tone' => 'audit-badge-neutral', 'mark' => '?'];
                $createdAtCaracas = $auditoria->created_at->copy()->setTimezone('America/Caracas');
            @endphp

            <article class="audit-event">
                <div class="audit-event-time">
                    <span>{{ $createdAtCaracas->format('d/m/Y') }}</span>
                    <strong>{{ $createdAtCaracas->format('g:i A') }}</strong>
                    <small>{{ $createdAtCaracas->format('s') }} seg</small>
                </div>

                <div class="audit-event-card">
                    <div class="audit-event-head">
                        <span class="audit-mark {{ $meta['tone'] }}">{{ $meta['mark'] }}</span>
                        <div>
                            <span class="audit-badge {{ $meta['tone'] }}">{!! $meta['label'] !!}</span>
                            <h2>{{ $auditoria->table_name }} <span>ID {{ $auditoria->record_id }}</span></h2>
                        </div>
                    </div>

                    <div class="audit-facts">
                        <div>
                            <span>Actor</span>
                            <strong>{{ $auditoria->actor_name ?: ucfirst($auditoria->actor_type) }}</strong>
                            <small>{{ $auditoria->actor_type }}</small>
                        </div>
                        <div>
                            <span>Origen</span>
                            <strong>{{ $auditoria->ip_address ?: 'Sin IP' }}</strong>
                            <small title="{{ $auditoria->user_agent }}">{{ $auditoria->user_agent ?: 'Sin navegador' }}</small>
                        </div>
                    </div>

                    <div class="audit-change-grid">
                        @if ($auditoria->old_values)
                            <details class="audit-change-card audit-change-before">
                                <summary>Valores anteriores</summary>
                                <pre>{{ json_encode($auditoria->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif

                        @if ($auditoria->new_values)
                            <details class="audit-change-card audit-change-after" open>
                                <summary>Valores posteriores</summary>
                                <pre>{{ json_encode($auditoria->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif

                        @unless ($auditoria->old_values || $auditoria->new_values)
                            <p class="audit-empty-change">Este evento no registr&oacute; valores comparables.</p>
                        @endunless
                    </div>
                </div>
            </article>
        @empty
            <div class="audit-empty-state">
                <span>0</span>
                <h2>No existen registros de auditor&iacute;a</h2>
                <p>Ajusta los filtros o vuelve al historial completo para revisar otros eventos.</p>
                <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Ver todo</a>
            </div>
        @endforelse
    </section>

    <div class="mt-6">
        {{ $auditorias->links() }}
    </div>
@endsection
