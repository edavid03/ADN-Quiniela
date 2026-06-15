@extends('layouts.app')

@section('title', 'Auditoria | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Administraci&oacute;n</span>
            <h1 class="page-title">Historial de auditor&iacute;a</h1>
            <p class="page-copy">Consulta las modificaciones realizadas en usuarios, equipos, partidos y predicciones.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.resultados.edit') }}" class="btn btn-secondary">Resultados</a>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Usuarios</a>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.auditoria.index') }}" class="surface-strong mb-6 grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
        <label class="grid gap-2 text-sm font-extrabold">
            Usuario
            <select name="actor_id" class="rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] px-3 py-2.5 text-[var(--app-text)]">
                <option value="">Todos</option>
                @foreach ($actors as $actor)
                    <option value="{{ $actor->actor_id }}" @selected((string) request('actor_id') === (string) $actor->actor_id)>{{ $actor->actor_name }}</option>
                @endforeach
            </select>
        </label>

        <label class="grid gap-2 text-sm font-extrabold">
            Acci&oacute;n
            <select name="action" class="rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] px-3 py-2.5 text-[var(--app-text)]">
                <option value="">Todas</option>
                <option value="created" @selected(request('action') === 'created')>Creaci&oacute;n</option>
                <option value="updated" @selected(request('action') === 'updated')>Actualizaci&oacute;n</option>
                <option value="deleted" @selected(request('action') === 'deleted')>Eliminaci&oacute;n</option>
            </select>
        </label>

        <label class="grid gap-2 text-sm font-extrabold">
            Tabla
            <select name="table_name" class="rounded-lg border border-[var(--app-border)] bg-[var(--app-panel-strong)] px-3 py-2.5 text-[var(--app-text)]">
                <option value="">Todas</option>
                @foreach ($tables as $table)
                    <option value="{{ $table }}" @selected(request('table_name') === $table)>{{ $table }}</option>
                @endforeach
            </select>
        </label>

        <div class="grid grid-cols-2 gap-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('admin.auditoria.index') }}" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <section class="surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-[var(--app-border)] bg-[var(--app-panel-soft)] text-xs font-black uppercase text-[var(--app-muted)]">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Actor</th>
                        <th class="px-4 py-3">Acci&oacute;n</th>
                        <th class="px-4 py-3">Registro</th>
                        <th class="px-4 py-3">Contexto</th>
                        <th class="px-4 py-3">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--app-border)]">
                    @forelse ($auditorias as $auditoria)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-4 py-4 font-semibold">{{ $auditoria->created_at->setTimezone('America/Caracas')->format('d/m/Y g:i:s A') }}</td>
                            <td class="px-4 py-4">
                                <strong class="block">{{ $auditoria->actor_name ?: ucfirst($auditoria->actor_type) }}</strong>
                                <span class="text-xs font-semibold uppercase text-[var(--app-muted)]">{{ $auditoria->actor_type }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-lg bg-[var(--app-panel-soft)] px-2 py-1 text-xs font-black uppercase">
                                    {!! ['created' => 'Creaci&oacute;n', 'updated' => 'Actualizaci&oacute;n', 'deleted' => 'Eliminaci&oacute;n'][$auditoria->action] ?? e($auditoria->action) !!}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <strong class="block">{{ $auditoria->table_name }}</strong>
                                <span class="text-xs font-semibold text-[var(--app-muted)]">ID {{ $auditoria->record_id }}</span>
                            </td>
                            <td class="max-w-56 px-4 py-4 text-xs font-semibold text-[var(--app-muted)]">
                                <span class="block">{{ $auditoria->ip_address ?: 'Sin IP' }}</span>
                                <span class="mt-1 block truncate" title="{{ $auditoria->user_agent }}">{{ $auditoria->user_agent ?: 'Sin navegador' }}</span>
                            </td>
                            <td class="min-w-72 px-4 py-4">
                                @if ($auditoria->old_values)
                                    <details class="mb-2">
                                        <summary class="cursor-pointer font-extrabold text-[var(--app-secondary)]">Valores anteriores</summary>
                                        <pre class="mt-2 max-h-72 overflow-auto rounded-lg bg-[var(--app-bg)] p-3 text-xs">{{ json_encode($auditoria->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                                @if ($auditoria->new_values)
                                    <details>
                                        <summary class="cursor-pointer font-extrabold text-[var(--app-success)]">Valores posteriores</summary>
                                        <pre class="mt-2 max-h-72 overflow-auto rounded-lg bg-[var(--app-bg)] p-3 text-xs">{{ json_encode($auditoria->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center font-semibold text-[var(--app-muted)]">No existen registros de auditor&iacute;a para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $auditorias->links() }}
    </div>
@endsection
