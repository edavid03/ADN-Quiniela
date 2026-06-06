<?php

use App\Http\Controllers\AdminPartidoResultadoController;
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
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $predictionDeadline = Partido::fechaLimiteApuestasUtc();

        return view('dashboard', [
            'teamCount' => Equipo::query()->count(),
            'matchCount' => Partido::query()->count(),
            'predictionCount' => Prediccion::query()
                ->where('usuario_id', $user->id)
                ->count(),
            'predictionDeadline' => $predictionDeadline,
            'nextMatches' => Partido::query()
                ->with(['local', 'visitante'])
                ->orderBy('fecha_utc')
                ->take(5)
                ->get(),
        ]);
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
    Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
    Route::get('/pronosticos', [PronosticoController::class, 'edit'])->name('pronosticos.edit');
    Route::post('/pronosticos', [PronosticoController::class, 'update'])->name('pronosticos.update');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminPartidoResultadoController::class, 'edit'])->name('admin.dashboard');
        Route::get('/admin/resultados', [AdminPartidoResultadoController::class, 'edit'])->name('admin.resultados.edit');
        Route::post('/admin/resultados', [AdminPartidoResultadoController::class, 'update'])->name('admin.resultados.update');
    });
});
