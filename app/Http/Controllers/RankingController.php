<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function index(): View
    {
        $rankings = User::query()
            ->where('users.is_admin', false)
            ->leftJoin('predicciones', 'users.id', '=', 'predicciones.usuario_id')
            ->select([
                'users.id',
                'users.name',
                'users.username',
                DB::raw('COALESCE(SUM(predicciones.puntos), 0) as total_puntos'),
                DB::raw('COUNT(predicciones.id) as pronosticos'),
                DB::raw('SUM(CASE WHEN predicciones.puntos IS NOT NULL THEN 1 ELSE 0 END) as evaluados'),
                DB::raw('SUM(CASE WHEN predicciones.acertado = 1 THEN 1 ELSE 0 END) as exactos'),
            ])
            ->groupBy('users.id', 'users.name', 'users.username')
            ->orderByDesc('total_puntos')
            ->orderByDesc('exactos')
            ->orderByDesc('evaluados')
            ->orderBy('users.name')
            ->get();

        return view('rankings.index', [
            'rankings' => $rankings,
        ]);
    }

    public function showPredicciones(User $user): View
    {
        abort_if($user->is_admin, 404);

        $partidos = Partido::query()
            ->with(['local', 'visitante'])
            ->cerradosParaPronosticos()
            ->orderBy('fecha_utc')
            ->get();

        return view('rankings.predicciones', [
            'usuario' => $user,
            'partidos' => $partidos,
            'predicciones' => Prediccion::query()
                ->where('usuario_id', $user->id)
                ->whereIn('partido_id', $partidos->pluck('id'))
                ->get()
                ->keyBy('partido_id'),
        ]);
    }
}
