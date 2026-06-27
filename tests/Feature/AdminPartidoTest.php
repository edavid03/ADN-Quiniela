<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPartidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_match_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/partidos')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('security_alert', 'No tienes permisos para acceder al panel de administracion.');
    }

    public function test_admin_can_view_match_management(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante] = $this->crearEquipos();
        $this->crearPartido($local, $visitante);

        $this->actingAs($admin)
            ->get('/admin/partidos')
            ->assertOk()
            ->assertSee('Partidos de la quiniela')
            ->assertSee('Abrir edici&oacute;n', false);
    }

    public function test_admin_match_management_only_lists_future_matches(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante] = $this->crearEquipos();

        Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-27 11:59:00',
            'estadio' => 'Estadio Cerrado',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-27 12:01:00',
            'estadio' => 'Estadio Futuro',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/partidos')
            ->assertOk()
            ->assertSee('Estadio Futuro')
            ->assertDontSee('Estadio Cerrado')
            ->assertDontSee('Fecha cerrada');

        Carbon::setTestNow();
    }

    public function test_admin_match_management_does_not_list_matches_with_predictions(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        [$local, $visitante] = $this->crearEquipos();

        $partidoConPronostico = Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-28 12:00:00',
            'estadio' => 'Estadio Con Pronostico',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        Prediccion::query()->create([
            'usuario_id' => $user->id,
            'partido_id' => $partidoConPronostico->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);

        Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-28 16:00:00',
            'estadio' => 'Estadio Sin Pronostico',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/partidos')
            ->assertOk()
            ->assertSee('Estadio Sin Pronostico')
            ->assertDontSee('Estadio Con Pronostico')
            ->assertDontSee('pron&oacute;sticos', false);

        Carbon::setTestNow();
    }

    public function test_admin_can_create_future_match_entering_caracas_time_saved_as_utc(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante] = $this->crearEquipos();

        $this->actingAs($admin)
            ->post('/admin/partidos', [
                'local_id' => $local->id,
                'visitante_id' => $visitante->id,
                'fecha_caracas' => '2026-07-01T20:00',
                'estadio' => 'Estadio Olimpico',
                'fase' => 'Grupos',
            ])
            ->assertRedirect('/admin/partidos')
            ->assertSessionHas('status', 'Partido creado correctamente.');

        $this->assertDatabaseHas('partidos', [
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-07-02 00:00:00',
            'estadio' => 'Estadio Olimpico',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_update_future_match_entering_caracas_time_saved_as_utc(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante, $tercero] = $this->crearEquipos();
        $partido = $this->crearPartido($local, $visitante);

        $this->actingAs($admin)
            ->patch("/admin/partidos/{$partido->id}", [
                'local_id' => $tercero->id,
                'visitante_id' => $visitante->id,
                'fecha_caracas' => '2026-07-03T21:30',
                'estadio' => 'Nuevo Estadio',
                'fase' => 'Octavos',
            ])
            ->assertRedirect('/admin/partidos')
            ->assertSessionHas('status', 'Partido actualizado correctamente.');

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'local_id' => $tercero->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-07-04 01:30:00',
            'estadio' => 'Nuevo Estadio',
            'fase' => 'Octavos',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_cannot_create_match_with_past_or_current_date(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante] = $this->crearEquipos();

        $this->actingAs($admin)
            ->post('/admin/partidos', [
                'local_id' => $local->id,
                'visitante_id' => $visitante->id,
                'fecha_caracas' => '2026-06-27T08:00',
                'estadio' => 'Estadio Cerrado',
                'fase' => 'Grupos',
            ])
            ->assertSessionHasErrors('fecha_caracas')
            ->assertSessionHas('security_alert', 'Intentaste crear un partido con una fecha que ya paso.');

        $this->assertDatabaseMissing('partidos', [
            'estadio' => 'Estadio Cerrado',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_cannot_update_past_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        [$local, $visitante] = $this->crearEquipos();
        $partido = Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-06-27 11:59:00',
            'estadio' => 'Viejo Estadio',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/partidos/{$partido->id}", [
                'local_id' => $local->id,
                'visitante_id' => $visitante->id,
                'fecha_caracas' => '2026-07-02T18:00',
                'estadio' => 'No Debe Guardar',
                'fase' => 'Grupos',
            ])
            ->assertSessionHas('security_alert', 'No se puede editar un partido cuya fecha ya paso.');

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'estadio' => 'Viejo Estadio',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_cannot_update_future_match_with_predictions(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-27 12:00:00', 'UTC'));

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        [$local, $visitante] = $this->crearEquipos();
        $partido = $this->crearPartido($local, $visitante);

        Prediccion::query()->create([
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/partidos/{$partido->id}", [
                'local_id' => $local->id,
                'visitante_id' => $visitante->id,
                'fecha_caracas' => '2026-07-02T18:00',
                'estadio' => 'No Debe Guardar',
                'fase' => 'Grupos',
            ])
            ->assertSessionHas('security_alert', 'No se puede editar un partido que ya tiene pronosticos registrados.');

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'estadio' => 'Estadio de Prueba',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_cannot_create_match_with_same_local_and_visitor(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$local] = $this->crearEquipos();

        $this->actingAs($admin)
            ->post('/admin/partidos', [
                'local_id' => $local->id,
                'visitante_id' => $local->id,
                'fecha_caracas' => now('America/Caracas')->addDay()->format('Y-m-d\TH:i'),
                'estadio' => 'Estadio Invalido',
                'fase' => 'Grupos',
            ])
            ->assertSessionHasErrors('local_id');

        $this->assertDatabaseMissing('partidos', [
            'estadio' => 'Estadio Invalido',
        ]);
    }

    /**
     * @return array<int, Equipo>
     */
    private function crearEquipos(): array
    {
        return [
            Equipo::query()->create(['id' => 1, 'name' => 'Local FC', 'code' => 'LOC', 'grupo' => 'A']),
            Equipo::query()->create(['id' => 2, 'name' => 'Visitante FC', 'code' => 'VIS', 'grupo' => 'A']),
            Equipo::query()->create(['id' => 3, 'name' => 'Tercero FC', 'code' => 'TER', 'grupo' => 'B']),
        ];
    }

    private function crearPartido(Equipo $local, Equipo $visitante): Partido
    {
        return Partido::query()->create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => '2026-07-01 22:00:00',
            'estadio' => 'Estadio de Prueba',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }
}
