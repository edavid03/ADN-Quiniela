<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function index(): View
    {
        $rankings = User::query()
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
}
