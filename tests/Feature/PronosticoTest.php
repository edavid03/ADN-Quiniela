<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PronosticoTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_create_pronosticos(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 2,
                        'goles_visitante' => 1,
                    ],
                ],
            ])
            ->assertRedirect('/pronosticos');

        $this->assertDatabaseHas('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
        ]);
    }

    public function test_users_can_update_existing_pronosticos(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 3,
                        'goles_visitante' => 2,
                    ],
                ],
            ])
            ->assertRedirect('/pronosticos');

        $this->assertDatabaseCount('predicciones', 1);
        $this->assertDatabaseHas('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 3,
            'goles_visitante' => 2,
        ]);
    }

    public function test_users_can_save_only_some_pronosticos(): void
    {
        $user = User::factory()->create();
        $primerPartido = $this->crearPartido();
        $segundoPartido = $this->crearPartido(3, 4);

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $primerPartido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => 0,
                    ],
                    $segundoPartido->id => [
                        'goles_local' => null,
                        'goles_visitante' => null,
                    ],
                ],
            ])
            ->assertRedirect('/pronosticos');

        $this->assertDatabaseCount('predicciones', 1);
        $this->assertDatabaseHas('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $primerPartido->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
        ]);
        $this->assertDatabaseMissing('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $segundoPartido->id,
        ]);
    }

    public function test_incomplete_pronostico_is_rejected(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => null,
                    ],
                ],
            ])
            ->assertSessionHasErrors('predicciones')
            ->assertSessionHas('security_alert', 'Intentaste guardar un pronostico incompleto.');

        $this->assertDatabaseCount('predicciones', 0);
    }

    public function test_incomplete_pronostico_shows_only_one_visible_alert(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        $this->actingAs($user)
            ->from('/pronosticos')
            ->followingRedirects()
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => null,
                    ],
                ],
            ])
            ->assertOk()
            ->assertSee('Intentaste guardar un pronostico incompleto.')
            ->assertDontSee('Cada pronostico debe tener goles de ambos equipos.');
    }

    public function test_pronosticos_after_deadline_are_rejected_with_security_alert(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartidoConFecha(now()->utc()->addMinutes(59));

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => 0,
                    ],
                ],
            ])
            ->assertSessionHasErrors('predicciones')
            ->assertSessionHas('security_alert', 'El plazo para registrar apuestas ha cerrado.');

        $this->assertDatabaseCount('predicciones', 0);
    }

    public function test_pronosticos_before_sixty_minute_deadline_are_allowed(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartidoConFecha(now()->utc()->addMinutes(61));

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => 0,
                    ],
                ],
            ])
            ->assertRedirect('/pronosticos');

        $this->assertDatabaseHas('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
        ]);
    }

    public function test_pronosticos_at_sixty_minute_deadline_are_rejected(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $user = User::factory()->create();
        $partido = $this->crearPartidoConFecha(now()->utc()->addMinutes(60));

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $partido->id => [
                        'goles_local' => 1,
                        'goles_visitante' => 0,
                    ],
                ],
            ])
            ->assertSessionHasErrors('predicciones');

        $this->assertDatabaseCount('predicciones', 0);
    }

    public function test_users_can_view_pronosticos_form_with_existing_values(): void
    {
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 1,
            'goles_visitante' => 1,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($user)
            ->get('/pronosticos')
            ->assertOk()
            ->assertSee('Mis pron&oacute;sticos', false)
            ->assertSee('value="1"', false);
    }

    public function test_pronosticos_form_only_shows_matches_that_are_still_open(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $user = User::factory()->create();
        $cerrado = $this->crearPartidoConFecha(now()->utc()->addMinutes(60));
        $abierto = $this->crearPartidoConFecha(now()->utc()->addMinutes(61), 3, 4);

        $this->actingAs($user)
            ->get('/pronosticos')
            ->assertOk()
            ->assertDontSee('predicciones['.$cerrado->id.']', false)
            ->assertSee('predicciones['.$abierto->id.']', false)
            ->assertSee('data-pronostico-partido', false);
    }

    public function test_closed_match_rejects_the_entire_prediction_batch(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $user = User::factory()->create();
        $abierto = $this->crearPartidoConFecha(now()->utc()->addMinutes(61));
        $cerrado = $this->crearPartidoConFecha(now()->utc()->addMinutes(60), 3, 4);

        $this->actingAs($user)
            ->post('/pronosticos', [
                'predicciones' => [
                    $abierto->id => ['goles_local' => 2, 'goles_visitante' => 1],
                    $cerrado->id => ['goles_local' => 0, 'goles_visitante' => 0],
                ],
            ])
            ->assertSessionHasErrors('predicciones')
            ->assertSessionHas('security_alert', 'El plazo para registrar apuestas ha cerrado.');

        $this->assertDatabaseCount('predicciones', 0);
    }

    public function test_empty_pronosticos_form_shows_no_available_matches_message(): void
    {
        $user = User::factory()->create();
        $this->crearPartidoConFecha(now()->utc()->addMinutes(60));

        $this->actingAs($user)
            ->get('/pronosticos')
            ->assertOk()
            ->assertSee('No hay partidos disponibles para pronosticar.')
            ->assertDontSee('data-pronosticos-submit', false);
    }

    public function test_dashboard_countdown_uses_the_next_individual_prediction_deadline(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $user = User::factory()->create();
        $this->crearPartidoConFecha(now()->utc()->addMinutes(30));
        $proximoPartidoAbierto = $this->crearPartidoConFecha(now()->utc()->addMinutes(90), 3, 4);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(
                'data-deadline="'.$proximoPartidoAbierto->fechaLimitePronosticoUtc()->toIso8601String().'"',
                false
            )
            ->assertSee('Pronosticos abiertos');
    }

    private function crearPartido(int $localId = 1, int $visitanteId = 2): Partido
    {
        return $this->crearPartidoConFecha(now()->utc()->addWeeks(3), $localId, $visitanteId);
    }

    private function crearPartidoConFecha($fechaUtc, int $localId = 1, int $visitanteId = 2): Partido
    {
        $local = Equipo::create([
            'id' => $localId,
            'name' => "Local {$localId} FC",
            'code' => 'LOC',
            'grupo' => 'A',
        ]);

        $visitante = Equipo::create([
            'id' => $visitanteId,
            'name' => "Visitante {$visitanteId} FC",
            'code' => 'VIS',
            'grupo' => 'A',
        ]);

        return Partido::create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => $fechaUtc->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio de Prueba',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }
}
