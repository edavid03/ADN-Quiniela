<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Prediccion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultadoController extends Controller
{
    public function index(Request $request): View
    {
        return view('resultados.index', [
            'partidos' => Partido::query()
                ->with(['local', 'visitante'])
                ->where('fecha_utc', '<=', now()->utc()->addHours(24))
                ->orderByDesc('fecha_utc')
                ->get(),
            'predicciones' => Prediccion::query()
                ->where('usuario_id', $request->user()->id)
                ->get()
                ->keyBy('partido_id'),
        ]);
    }
}
