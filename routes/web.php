<?php

use App\Http\Controllers\AdminAuditoriaController;
use App\Http\Controllers\AdminPartidoController;
use App\Http\Controllers\AdminPartidoResultadoController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PronosticoController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ResultadoController;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $nextOpenMatch = Partido::query()
            ->with(['local', 'visitante'])
            ->abiertosParaPronosticos()
            ->orderBy('fecha_utc')
            ->first();
        $predictionDeadline = $nextOpenMatch?->fechaLimitePronosticoUtc();

        return view('dashboard', [
            'teamCount' => Equipo::query()->count(),
            'matchCount' => Partido::query()->count(),
            'predictionCount' => Prediccion::query()
                ->where('usuario_id', $user->id)
                ->count(),
            'predictionDeadline' => $predictionDeadline,
            'nextOpenMatch' => $nextOpenMatch,
            'nextMatches' => Partido::query()
                ->with(['local', 'visitante'])
                ->where('fecha_utc', '>', now()->utc())
                ->orderBy('fecha_utc')
                ->take(5)
                ->get(),
        ]);
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
    Route::get('/rankings/{user}/predicciones', [RankingController::class, 'showPredicciones'])->name('rankings.predicciones.show');
    Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
    Route::view('/reglas', 'reglas.index')->name('reglas.index');

    Route::middleware('not_admin')->group(function () {
        Route::get('/pronosticos', [PronosticoController::class, 'edit'])->name('pronosticos.edit');
        Route::post('/pronosticos', [PronosticoController::class, 'update'])->name('pronosticos.update');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/admin/auditoria', [AdminAuditoriaController::class, 'index'])->name('admin.auditoria.index');
        Route::get('/admin/dashboard', [AdminPartidoResultadoController::class, 'edit'])->name('admin.dashboard');
        Route::get('/admin/partidos', [AdminPartidoController::class, 'index'])->name('admin.partidos.index');
        Route::post('/admin/partidos', [AdminPartidoController::class, 'store'])->name('admin.partidos.store');
        Route::patch('/admin/partidos/{partido}', [AdminPartidoController::class, 'update'])->name('admin.partidos.update');
        Route::get('/admin/resultados', [AdminPartidoResultadoController::class, 'edit'])->name('admin.resultados.edit');
        Route::post('/admin/resultados', [AdminPartidoResultadoController::class, 'update'])->name('admin.resultados.update');
        Route::get('/admin/usuarios', [AdminUserController::class, 'index'])->name('admin.usuarios.index');
        Route::post('/admin/usuarios', [AdminUserController::class, 'store'])->name('admin.usuarios.store');
        Route::patch('/admin/usuarios/{user}/aceptar', [AdminUserController::class, 'approve'])->name('admin.usuarios.approve');
        Route::delete('/admin/usuarios/{user}', [AdminUserController::class, 'destroy'])->name('admin.usuarios.destroy');
    });
});
