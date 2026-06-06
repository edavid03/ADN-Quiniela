<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Prediccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PronosticoController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $partidos = Partido::query()
            ->with(['local', 'visitante'])
            ->orderBy('fecha_utc')
            ->get();

        $predicciones = Prediccion::query()
            ->where('usuario_id', $user->id)
            ->get()
            ->keyBy('partido_id');

        return view('pronosticos.edit', [
            'partidos' => $partidos,
            'predicciones' => $predicciones,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'predicciones' => ['required', 'array'],
            'predicciones.*.goles_local' => ['nullable', 'integer', 'min:0', 'max:99'],
            'predicciones.*.goles_visitante' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $pronosticos = $validated['predicciones'];
        $pronosticosCompletos = [];

        foreach ($pronosticos as $partidoId => $pronostico) {
            $golesLocal = $pronostico['goles_local'] ?? null;
            $golesVisitante = $pronostico['goles_visitante'] ?? null;

            if ($golesLocal === null && $golesVisitante === null) {
                continue;
            }

            if ($golesLocal === null || $golesVisitante === null) {
                return back()
                    ->withErrors(['predicciones' => 'Cada pronostico debe tener goles de ambos equipos.'])
                    ->with('security_alert', 'Intentaste guardar un pronostico incompleto.')
                    ->withInput();
            }

            $pronosticosCompletos[$partidoId] = $pronostico;
        }

        if ($pronosticosCompletos === []) {
            return redirect()
                ->route('pronosticos.edit')
                ->with('status', 'No se realizaron cambios.');
        }

        $partidoIds = array_map('intval', array_keys($pronosticosCompletos));
        $partidosExistentes = Partido::query()
            ->whereIn('id', $partidoIds)
            ->pluck('id')
            ->all();

        if (count($partidosExistentes) !== count($partidoIds)) {
            return back()
                ->withErrors(['predicciones' => 'Uno de los partidos seleccionados no existe.'])
                ->with('security_alert', 'Se detecto un partido invalido en el formulario.')
                ->withInput();
        }

        foreach ($pronosticosCompletos as $partidoId => $pronostico) {
            $resultado = Prediccion::registrarApuesta(
                $request->user()->id,
                (int) $partidoId,
                (int) $pronostico['goles_local'],
                (int) $pronostico['goles_visitante'],
            );

            if (is_string($resultado)) {
                return back()
                    ->withErrors(['predicciones' => $resultado])
                    ->with('security_alert', $resultado)
                    ->withInput();
            }
        }

        return redirect()
            ->route('pronosticos.edit')
            ->with('status', 'Pronosticos guardados correctamente.');
    }
}
