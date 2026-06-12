<?php

namespace App\Http\Controllers;

use App\Models\Regla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReglaController extends Controller
{
    public function index(): View
    {
        return view('reglas.index', [
            'reglas' => Regla::query()
                ->with('updatedBy')
                ->orderBy('orden')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRegla($request);

        Regla::create([
            ...$validated,
            'orden' => (int) Regla::max('orden') + 1,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('reglas.index')
            ->with('status', 'Regla guardada correctamente.');
    }

    public function update(Request $request, Regla $regla): RedirectResponse
    {
        $regla->update([
            ...$this->validateRegla($request),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('reglas.index')
            ->with('status', 'Regla actualizada correctamente.');
    }

    public function destroy(Regla $regla): RedirectResponse
    {
        $regla->delete();

        return redirect()
            ->route('reglas.index')
            ->with('status', 'Regla eliminada correctamente.');
    }

    /**
     * @return array{titulo: string, contenido: string}
     */
    private function validateRegla(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:120'],
            'contenido' => ['required', 'string', 'max:2000'],
        ]);
    }
}
