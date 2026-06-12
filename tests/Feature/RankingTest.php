<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_rankings(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana',
            'username' => 'ana',
        ]);

        $this->actingAs($user)
            ->get('/rankings')
            ->assertOk()
            ->assertSee('Ranking')
            ->assertSee('Ana');
    }

    public function test_dashboard_links_to_rankings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ver ranking');
    }

    public function test_rankings_are_ordered_by_points(): void
    {
        $partido = $this->crearPartido();
        $ana = User::factory()->create([
            'name' => 'Ana',
            'username' => 'ana',
        ]);
        $bruno = User::factory()->create([
            'name' => 'Bruno',
            'username' => 'bruno',
        ]);

        Prediccion::create([
            'usuario_id' => $ana->id,
            'partido_id' => $partido->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => 1,
        ]);

        Prediccion::create([
            'usuario_id' => $bruno->id,
            'partido_id' => $partido->id,
            'goles_local' => 2,
            'goles_visitante' => 0,
            'acertado' => true,
            'puntos' => 3,
        ]);

        $this->actingAs($ana)
            ->get('/rankings')
            ->assertOk()
            ->assertSeeInOrder(['Bruno', 'Ana']);
    }

    public function test_admin_is_not_listed_in_rankings(): void
    {
        $user = User::factory()->create([
            'name' => 'Participante',
            'username' => 'participante',
        ]);
        User::factory()->create([
            'name' => 'Administrador',
            'username' => 'administrador',
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/rankings')
            ->assertOk()
            ->assertSee('Participante')
            ->assertDontSee('Administrador');
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
