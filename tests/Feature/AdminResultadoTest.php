<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AdminResultadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_admin_user_can_exist(): void
    {
        User::factory()->create([
            'is_admin' => true,
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Solo puede existir un usuario administrador.');

        User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_non_admin_users_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('security_alert', 'No tienes permisos para acceder al panel de administracion.');
    }

    public function test_non_admin_json_requests_are_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_view_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Resultados de partidos');
    }

    public function test_admin_can_update_match_results_and_recalculate_prediction_points(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($admin)
            ->post('/admin/resultados', [
                'resultados' => [
                    $partido->id => [
                        'goles_local' => 2,
                        'goles_visitante' => 1,
                    ],
                ],
            ])
            ->assertRedirect('/admin/resultados');

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
        ]);

        $this->assertDatabaseHas('predicciones', [
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'acertado' => true,
            'puntos' => 3,
        ]);
    }

    public function test_incomplete_admin_result_shows_security_alert(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $partido = $this->crearPartido();

        $this->actingAs($admin)
            ->post('/admin/resultados', [
                'resultados' => [
                    $partido->id => [
                        'goles_local' => 2,
                        'goles_visitante' => null,
                    ],
                ],
            ])
            ->assertSessionHasErrors('resultados')
            ->assertSessionHas('security_alert', 'Intentaste guardar un resultado incompleto.');

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }

    public function test_incomplete_admin_result_shows_only_one_visible_alert(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $partido = $this->crearPartido();

        $this->actingAs($admin)
            ->from('/admin/resultados')
            ->followingRedirects()
            ->post('/admin/resultados', [
                'resultados' => [
                    $partido->id => [
                        'goles_local' => 2,
                        'goles_visitante' => null,
                    ],
                ],
            ])
            ->assertOk()
            ->assertSee('Intentaste guardar un resultado incompleto.')
            ->assertDontSee('Cada resultado debe tener goles de ambos equipos.');
    }

    public function test_incomplete_result_rejects_the_entire_batch(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $primerPartido = $this->crearPartido();
        $segundoPartido = $this->crearPartido(3, 4);

        $this->actingAs($admin)
            ->post('/admin/resultados', [
                'resultados' => [
                    $primerPartido->id => ['goles_local' => 2, 'goles_visitante' => 1],
                    $segundoPartido->id => ['goles_local' => 1, 'goles_visitante' => null],
                ],
            ])
            ->assertSessionHasErrors('resultados');

        $this->assertDatabaseHas('partidos', [
            'id' => $primerPartido->id,
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }

    private function crearPartido(int $localId = 1, int $visitanteId = 2): Partido
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
            'fecha_utc' => now()->utc()->addWeeks(3)->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio de Prueba',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }
}
