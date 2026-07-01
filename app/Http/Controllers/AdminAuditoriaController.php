<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AdminAuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'actor_id' => ['nullable', 'integer'],
            'actor_ids' => ['nullable', 'array'],
            'actor_ids.*' => ['integer'],
            'action' => ['nullable', 'in:created,updated,deleted'],
            'table_name' => ['nullable', 'string', 'max:255'],
        ]);

        $actorIds = collect(Arr::wrap($filters['actor_ids'] ?? $filters['actor_id'] ?? []))
            ->filter(fn ($actorId) => $actorId !== null && $actorId !== '')
            ->map(fn ($actorId) => (int) $actorId)
            ->unique()
            ->values();

        $auditorias = Auditoria::query()
            ->with('actor')
            ->when($actorIds->isNotEmpty(), fn ($query) => $query->whereIn('actor_id', $actorIds))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['table_name'] ?? null, fn ($query, $tableName) => $query->where('table_name', $tableName))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.auditoria', [
            'auditorias' => $auditorias,
            'actors' => Auditoria::query()
                ->leftJoin('users', 'users.id', '=', 'auditorias.actor_id')
                ->whereNotNull('actor_id')
                ->selectRaw('auditorias.actor_id, auditorias.actor_name, users.is_admin')
                ->groupBy('auditorias.actor_id', 'auditorias.actor_name', 'users.is_admin')
                ->orderBy('actor_name')
                ->get(),
            'selectedActorIds' => $actorIds,
            'tables' => Auditoria::query()
                ->select('table_name')
                ->distinct()
                ->orderBy('table_name')
                ->pluck('table_name'),
        ]);
    }
}
