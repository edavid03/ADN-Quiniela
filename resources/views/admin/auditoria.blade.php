@extends('layouts.app')

@section('title', 'Auditoria | '.config('app.name', 'Quiniela'))

@section('content')
    @php
        $actionMeta = [
            'created' => ['label' => 'Creaci&oacute;n', 'tone' => 'audit-badge-create'],
            'updated' => ['label' => 'Actualizaci&oacute;n', 'tone' => 'audit-badge-update'],
            'deleted' => ['label' => 'Eliminaci&oacute;n', 'tone' => 'audit-badge-delete'],
        ];

        $activeFilters = collect([
            $selectedActorIds->isNotEmpty() ? 'Usuarios' : null,
            request('action') ? 'Acci&oacute;n' : null,
            request('table_name') ? 'Tabla' : null,
        ])->filter()->count();

        $selectedActorNames = $actors
            ->filter(fn ($actor) => $selectedActorIds->contains((int) $actor->actor_id))
            ->pluck('actor_name')
            ->values();

        $formatAuditJson = function ($value) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        };
    @endphp

    <section class="page-header audit-header">
        <div>
            <span class="kicker">Administraci&oacute;n</span>
            <h1>Historial de auditor&iacute;a</h1>
            <p>Consulta qui&eacute;n hizo cada cambio, cu&aacute;ndo ocurri&oacute; y qu&eacute; datos se modificaron.</p>
        </div>

        <div class="audit-summary" aria-label="Resumen de auditoria">
            <div>
                <span>Eventos</span>
                <strong>{{ $auditorias->total() }}</strong>
            </div>
            <div>
                <span>Filtros</span>
                <strong>{{ $activeFilters }}</strong>
            </div>
            <div>
                <span>En p&aacute;gina</span>
                <strong>{{ $auditorias->count() }}</strong>
            </div>
        </div>
    </section>

    <div class="audit-toolbar">
        <form method="GET" action="{{ route('admin.auditoria.index') }}" class="audit-filters">
            <fieldset class="audit-user-filter">
                <legend>
                    <span>Usuarios</span>
                    <small>{{ $selectedActorIds->isNotEmpty() ? $selectedActorIds->count().' seleccionados' : 'Todos' }}</small>
                </legend>

                <div class="audit-user-select">
                    <button type="button" class="audit-user-select-button" data-audit-user-modal-open aria-haspopup="dialog" aria-controls="audit-user-modal">
                        <span>
                            <strong>{{ $selectedActorIds->isNotEmpty() ? $selectedActorIds->count().' usuarios elegidos' : 'Todos los usuarios' }}</strong>
                            <small>
                                @if ($selectedActorNames->isNotEmpty())
                                    {{ $selectedActorNames->take(3)->join(', ') }}{{ $selectedActorNames->count() > 3 ? ' +' . ($selectedActorNames->count() - 3) : '' }}
                                @else
                                    Sin filtro por usuario
                                @endif
                            </small>
                        </span>
                        <span aria-hidden="true">Editar</span>
                    </button>
                </div>

                <dialog id="audit-user-modal" class="audit-user-modal" data-audit-user-modal aria-labelledby="audit-user-modal-title">
                    <div class="audit-user-modal-panel">
                        <header class="audit-user-modal-head">
                            <div>
                                <span>Filtro de usuarios</span>
                                <h2 id="audit-user-modal-title">Elige que usuarios quieres ver</h2>
                            </div>
                            <button type="button" class="audit-modal-close" data-audit-user-modal-close aria-label="Cerrar selector">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </header>

                        <div class="audit-user-modal-tools">
                            <label>
                                <span>Buscar usuario</span>
                                <input type="search" placeholder="Nombre, usuario o rol" data-audit-user-search>
                            </label>
                            <div>
                                <button type="button" class="btn btn-secondary" data-audit-user-check-all>Marcar visibles</button>
                                <button type="button" class="btn btn-secondary" data-audit-user-clear>Limpiar</button>
                            </div>
                        </div>

                        <div class="audit-user-modal-status" aria-live="polite">
                            <div>
                                <span>Seleccionados</span>
                                <strong data-audit-user-selected-count>{{ $selectedActorIds->count() }}</strong>
                            </div>
                            <div>
                                <span>Visibles</span>
                                <strong data-audit-user-visible-count>{{ $actors->count() }}</strong>
                            </div>
                            <p data-audit-user-selection-text>
                                {{ $selectedActorIds->isNotEmpty() ? 'Se mostraran eventos solo de los usuarios marcados.' : 'Sin usuarios marcados: se mostraran todos los eventos.' }}
                            </p>
                        </div>

                        <div class="audit-user-role-tabs" role="group" aria-label="Filtrar lista de usuarios">
                            <button type="button" class="is-active" data-audit-user-role-filter="all">Todos</button>
                            <button type="button" data-audit-user-role-filter="admin">Admins</button>
                            <button type="button" data-audit-user-role-filter="user">Usuarios</button>
                            <button type="button" data-audit-user-role-filter="selected">Marcados</button>
                        </div>

                        <div class="audit-user-picker" data-audit-user-list>
                            @forelse ($actors as $actor)
                                <label
                                    data-audit-user-option
                                    data-audit-user-role="{{ $actor->is_admin ? 'admin' : 'user' }}"
                                    data-audit-user-name="{{ \Illuminate\Support\Str::lower($actor->actor_name.' '.($actor->is_admin ? 'admin' : 'usuario')) }}"
                                >
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
                            @empty
                                <p class="audit-user-picker-empty">No hay usuarios con eventos registrados.</p>
                            @endforelse
                        </div>

                        <p class="audit-user-no-results" data-audit-user-no-results hidden>No hay usuarios que coincidan con esa busqueda.</p>

                        <footer class="audit-user-modal-actions">
                            <button type="button" class="btn btn-secondary" data-audit-user-modal-close>Cancelar</button>
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </footer>
                    </div>
                </dialog>
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
                $meta = $actionMeta[$auditoria->action] ?? ['label' => e($auditoria->action), 'tone' => 'audit-badge-neutral'];
                $createdAtCaracas = $auditoria->created_at->copy()->setTimezone('America/Caracas');
                $oldValues = $auditoria->old_values;
                $newValues = $auditoria->new_values;
                $hasAuditValues = $oldValues !== null || $newValues !== null;
            @endphp

            <article class="audit-event">
                <div class="audit-event-card">
                    <div class="audit-event-head">
                        <div>
                            <span class="audit-badge {{ $meta['tone'] }}">{!! $meta['label'] !!}</span>
                            <h2>{{ $auditoria->table_name }} <span>ID {{ $auditoria->record_id }}</span></h2>
                        </div>
                        <time datetime="{{ $auditoria->created_at->toIso8601String() }}">
                            {{ $createdAtCaracas->format('d/m/Y') }} · {{ $createdAtCaracas->format('g:i A') }}
                        </time>
                    </div>

                    <div class="audit-facts">
                        <div>
                            <span>Usuario</span>
                            <strong>{{ $auditoria->actor_name ?: ucfirst($auditoria->actor_type) }}</strong>
                            <small>{{ $auditoria->actor_type }}</small>
                        </div>
                        <div>
                            <span>Origen</span>
                            <strong>{{ $auditoria->ip_address ?: 'Sin IP' }}</strong>
                            <small title="{{ $auditoria->user_agent }}">{{ $auditoria->user_agent ?: 'Sin navegador' }}</small>
                        </div>
                    </div>

                    <div class="audit-changes">
                        @if ($hasAuditValues)
                            <div class="audit-json-grid">
                                <div class="audit-json-block audit-json-old">
                                    <strong>old:</strong>
                                    <pre>{{ $formatAuditJson($oldValues) }}</pre>
                                </div>
                                <div class="audit-json-block audit-json-new">
                                    <strong>new:</strong>
                                    <pre>{{ $formatAuditJson($newValues) }}</pre>
                                </div>
                            </div>
                        @else
                            <p class="audit-empty-change">Este evento no registr&oacute; valores comparables.</p>
                        @endif
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
