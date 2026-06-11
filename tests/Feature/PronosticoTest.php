<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PronosticoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_pronosticos_route(): void
    {
        $this->get('/pronosticos')->assertNotFound();
    }

    public function test_users_cannot_access_pronosticos_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pronosticos')
            ->assertNotFound();
    }

    public function test_admins_cannot_access_pronosticos_route(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/pronosticos')
            ->assertNotFound();
    }

    public function test_users_cannot_create_or_update_pronosticos(): void
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
            ->assertNotFound();

        $this->assertDatabaseCount('predicciones', 0);
    }

    private function crearPartido(): Partido
    {
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
