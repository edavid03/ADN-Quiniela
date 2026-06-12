<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPartidoResultadoController extends Controller
{
    public function edit(): View
    {
        return view('admin.resultados', [
            'partidos' => Partido::query()
                ->with(['local', 'visitante'])
                ->orderBy('fecha_utc')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'resultados' => ['required', 'array'],
            'resultados.*.goles_local' => ['nullable', 'integer', 'min:0', 'max:99'],
            'resultados.*.goles_visitante' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $resultados = $validated['resultados'];
        $partidoIds = array_map('intval', array_keys($resultados));
        $partidos = Partido::query()
            ->whereIn('id', $partidoIds)
            ->get()
            ->keyBy('id');

        if ($partidos->count() !== count($partidoIds)) {
            return back()
                ->withErrors(['resultados' => 'Uno de los partidos seleccionados no existe.'])
                ->with('security_alert', 'Se detecto un partido invalido en el formulario.')
                ->withInput();
        }

        $resultadosCompletos = [];

        foreach ($resultados as $partidoId => $resultado) {
            $golesLocal = $resultado['goles_local'] ?? null;
            $golesVisitante = $resultado['goles_visitante'] ?? null;

            if ($golesLocal === null && $golesVisitante === null) {
                continue;
            }

            if ($golesLocal === null || $golesVisitante === null) {
                return back()
                    ->withErrors(['resultados' => 'Cada resultado debe tener goles de ambos equipos.'])
                    ->with('security_alert', 'Intentaste guardar un resultado incompleto.')
                    ->withInput();
            }

            $resultadosCompletos[$partidoId] = [
                'goles_local' => (int) $golesLocal,
                'goles_visitante' => (int) $golesVisitante,
            ];
        }

        DB::transaction(function () use ($resultadosCompletos, $partidos): void {
            foreach ($resultadosCompletos as $partidoId => $resultado) {
                $partidos->get((int) $partidoId)->finalizarPartido(
                    $resultado['goles_local'],
                    $resultado['goles_visitante'],
                );
            }
        });

        return redirect()
            ->route('admin.resultados.edit')
            ->with('status', 'Resultados guardados correctamente.');
    }
}
