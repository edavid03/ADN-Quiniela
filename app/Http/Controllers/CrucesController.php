<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Services\WorldCup\BracketResolver;
use Illuminate\View\View;

class CrucesController extends Controller
{
    public function index(BracketResolver $resolver): View
    {
        $partidos = Partido::query()
            ->with(['local', 'visitante'])
            ->orderBy('fecha_utc')
            ->get();

        return view('cruces.index', [
            'bracket' => $resolver->resolve($partidos),
        ]);
    }
}
