<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\User;
use Illuminate\View\View;

class PronosticoPublicoController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where('is_admin', false)
            ->whereNotNull('approved_at')
            ->orderBy('name')
            ->get();

        $partidos = Partido::query()
            ->whereNotNull('goles_local')
            ->whereNotNull('goles_visitante')
            ->with(['local', 'visitante', 'predicciones' => fn ($q) => $q->with('usuario')])
            ->orderByDesc('fecha_utc')
            ->get();

        return view('pronosticos-publicos.index', [
            'partidos' => $partidos,
            'users' => $users,
        ]);
    }
}
