<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_match_results(): void
    {
        $user = User::factory()->create();
        $local = Equipo::create([
            'id' => 1,
            'name' => 'Local FC',
            'code' => 'LOC',
            'grupo' => 'A',
        ]);
        $visitante = Equipo::create([
            'id' => 2,
            'name' => 'Visitante FC',
            'code' => 'VIS',
            'grupo' => 'A',
        ]);

        $partidoFinalizado = Partido::create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => now()->utc()->addDay()->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio Final',
            'fase' => 'Grupos',
            'goles_local' => 2,
            'goles_visitante' => 1,
        ]);

        $partidoPendiente = Partido::create([
            'local_id' => $visitante->id,
            'visitante_id' => $local->id,
            'fecha_utc' => now()->utc()->addDays(2)->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio Pendiente',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
        $partidoNoAcertado = Partido::create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => now()->utc()->addDays(3)->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio No Acertado',
            'fase' => 'Grupos',
            'goles_local' => 1,
            'goles_visitante' => 1,
        ]);

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partidoFinalizado->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
            'acertado' => true,
            'puntos' => 3,
        ]);

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partidoPendiente->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);
        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partidoNoAcertado->id,
            'goles_local' => 0,
            'goles_visitante' => 2,
            'acertado' => false,
            'puntos' => 0,
        ]);

        $this->actingAs($user)
            ->get('/resultados')
            ->assertOk()
            ->assertSee('Resultados de partidos')
            ->assertSee('2 - 1')
            ->assertSee('Finalizado')
            ->assertSee('Tu pronostico')
            ->assertSee('Acertaste')
            ->assertSee('3 pts')
            ->assertSee('0 - 0')
            ->assertSee('Pendiente de resultado')
            ->assertSee('No acertaste')
            ->assertSee('0 pts');
    }

    public function test_match_times_are_displayed_in_caracas_timezone(): void
    {
        $user = User::factory()->create();
        $local = Equipo::create([
            'id' => 1,
            'name' => 'Local FC',
            'code' => 'LOC',
            'grupo' => 'A',
        ]);
        $visitante = Equipo::create([
            'id' => 2,
            'name' => 'Visitante FC',
            'code' => 'VIS',
            'grupo' => 'A',
        ]);

        Partido::create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-12 00:30:00',
            'estadio' => 'Estadio Caracas',
            'fase' => 'Grupos',
        ]);

        $this->actingAs($user)
            ->get('/resultados')
            ->assertOk()
            ->assertSee('11/06/2026 8:30 PM')
            ->assertSee('hora de Caracas')
            ->assertDontSee('11/06/2026 20:30')
            ->assertDontSee('12/06/2026 00:30 UTC');
    }
}
