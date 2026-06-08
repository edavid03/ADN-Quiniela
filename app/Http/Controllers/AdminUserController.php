<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.usuarios', [
            'pendingUsers' => User::query()
                ->whereNull('approved_at')
                ->orderBy('created_at')
                ->get(),
            'approvedUsers' => User::query()
                ->whereNotNull('approved_at')
                ->withCount('predicciones')
                ->orderByDesc('is_admin')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        User::create([
            ...$validated,
            'approved_at' => now(),
            'is_admin' => false,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario creado y aprobado correctamente.');
    }

    public function approve(User $user): RedirectResponse
    {
        if ($user->isApproved()) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('status', 'El usuario ya estaba aprobado.');
        }

        $user->update(['approved_at' => now()]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario aceptado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin || $request->user()->is($user)) {
            return back()->with('security_alert', 'No se puede eliminar al usuario administrador.');
        }

        if ($user->predicciones()->exists()) {
            return back()->with('security_alert', 'No se puede eliminar un usuario que tenga pronosticos registrados.');
        }

        $user->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario eliminado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'cedula' => ['required', 'string', 'regex:/^\d{6,12}$/', 'unique:users,cedula'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
    }
}
