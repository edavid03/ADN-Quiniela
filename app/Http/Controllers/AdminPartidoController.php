<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPartidoController extends Controller
{
    private const CARACAS_TIMEZONE = 'America/Caracas';

    public function index(): View
    {
        return view('admin.partidos', [
            'equipos' => Equipo::query()
                ->orderBy('grupo')
                ->orderBy('name')
                ->get(),
            'partidos' => Partido::query()
                ->with(['local', 'visitante'])
                ->whereDoesntHave('predicciones')
                ->where('fecha_utc', '>', now()->utc())
                ->orderBy('fecha_utc')
                ->get(),
            'minimumCaracasDateTime' => now(self::CARACAS_TIMEZONE)->addMinute()->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePartido($request);
        $fechaUtc = $this->caracasDateTimeToUtc($validated['fecha_caracas']);

        if ($fechaUtc->lessThanOrEqualTo(now()->utc())) {
            return back()
                ->withErrors(['fecha_caracas' => 'Solo puedes crear partidos con fecha posterior al momento actual.'])
                ->with('security_alert', 'Intentaste crear un partido con una fecha que ya paso.')
                ->withInput();
        }

        Partido::query()->create([
            'local_id' => $validated['local_id'],
            'visitante_id' => $validated['visitante_id'],
            'fecha_utc' => $fechaUtc->format('Y-m-d H:i:s'),
            'estadio' => $validated['estadio'] ?? null,
            'fase' => $validated['fase'],
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        return redirect()
            ->route('admin.partidos.index')
            ->with('status', 'Partido creado correctamente.');
    }

    public function update(Request $request, Partido $partido): RedirectResponse
    {
        $partido->loadCount('predicciones');

        if ($partido->fecha_utc->copy()->utc()->lessThanOrEqualTo(now()->utc())) {
            return back()
                ->with('security_alert', 'No se puede editar un partido cuya fecha ya paso.')
                ->withInput();
        }

        if ($partido->predicciones_count > 0) {
            return back()
                ->with('security_alert', 'No se puede editar un partido que ya tiene pronosticos registrados.')
                ->withInput();
        }

        $validated = $this->validatePartido($request);
        $fechaUtc = $this->caracasDateTimeToUtc($validated['fecha_caracas']);

        if ($fechaUtc->lessThanOrEqualTo(now()->utc())) {
            return back()
                ->withErrors(['fecha_caracas' => 'Solo puedes editar partidos con fecha posterior al momento actual.'])
                ->with('security_alert', 'Intentaste guardar un partido con una fecha que ya paso.')
                ->withInput();
        }

        $partido->update([
            'local_id' => $validated['local_id'],
            'visitante_id' => $validated['visitante_id'],
            'fecha_utc' => $fechaUtc->format('Y-m-d H:i:s'),
            'estadio' => $validated['estadio'] ?? null,
            'fase' => $validated['fase'],
        ]);

        return redirect()
            ->route('admin.partidos.index')
            ->with('status', 'Partido actualizado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePartido(Request $request): array
    {
        return $request->validate([
            'local_id' => ['required', 'integer', 'exists:equipos,id', 'different:visitante_id'],
            'visitante_id' => ['required', 'integer', 'exists:equipos,id'],
            'fecha_caracas' => ['required', 'date_format:Y-m-d\TH:i'],
            'estadio' => ['nullable', 'string', 'max:255'],
            'fase' => ['required', 'string', 'max:30', Rule::notIn([''])],
        ]);
    }

    private function caracasDateTimeToUtc(string $dateTime): Carbon
    {
        return Carbon::createFromFormat('Y-m-d\TH:i', $dateTime, self::CARACAS_TIMEZONE)->utc();
    }
}
