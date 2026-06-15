<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'actor_id' => ['nullable', 'integer'],
            'action' => ['nullable', 'in:created,updated,deleted'],
            'table_name' => ['nullable', 'string', 'max:255'],
        ]);

        $auditorias = Auditoria::query()
            ->with('actor')
            ->when($filters['actor_id'] ?? null, fn ($query, $actorId) => $query->where('actor_id', $actorId))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['table_name'] ?? null, fn ($query, $tableName) => $query->where('table_name', $tableName))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.auditoria', [
            'auditorias' => $auditorias,
            'actors' => Auditoria::query()
                ->whereNotNull('actor_id')
                ->select(['actor_id', 'actor_name'])
                ->distinct()
                ->orderBy('actor_name')
                ->get(),
            'tables' => Auditoria::query()
                ->select('table_name')
                ->distinct()
                ->orderBy('table_name')
                ->pluck('table_name'),
        ]);
    }
}
