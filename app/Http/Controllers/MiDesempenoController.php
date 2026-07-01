<?php

namespace App\Http\Controllers;

use App\Models\Prediccion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiDesempenoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $base = Prediccion::query()->where('usuario_id', $user->id);

        $puntos = (int) (clone $base)->sum('puntos');
        $pronosticos = (clone $base)->count();
        $evaluados = (clone $base)->whereNotNull('puntos')->count();
        $exactos = (clone $base)->where('acertado', true)->count();
        $aciertos = (clone $base)->where('puntos', '>', 0)->count();
        $signos = $aciertos - $exactos;
        $efectividad = $evaluados > 0 ? (int) round($aciertos / $evaluados * 100) : 0;

        $ranking = RankingController::rankingQuery()->get();
        $indice = $ranking->search(fn ($fila) => (int) $fila->id === $user->id);
        $posicion = $indice === false ? null : $indice + 1;

        return view('mi-desempeno.index', [
            'puntos' => $puntos,
            'pronosticos' => $pronosticos,
            'evaluados' => $evaluados,
            'exactos' => $exactos,
            'signos' => $signos,
            'aciertos' => $aciertos,
            'efectividad' => $efectividad,
            'posicion' => $posicion,
            'totalJugadores' => $ranking->count(),
        ]);
    }
}
