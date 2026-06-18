<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Carbon\Carbon;
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

    public function test_ranking_links_to_each_users_predictions(): void
    {
        $viewer = User::factory()->create();
        $participant = User::factory()->create([
            'name' => 'Participante',
            'username' => 'participante',
        ]);

        $this->actingAs($viewer)
            ->get('/rankings')
            ->assertOk()
            ->assertSee(route('rankings.predicciones.show', $participant), false)
            ->assertSee('Ver predicciones de Participante');
    }

    public function test_logged_user_is_highlighted_in_rankings(): void
    {
        $viewer = User::factory()->create([
            'name' => 'Usuario actual',
            'username' => 'actual',
        ]);
        User::factory()->create([
            'name' => 'Otro participante',
            'username' => 'otro',
        ]);

        $this->actingAs($viewer)
            ->get('/rankings')
            ->assertOk()
            ->assertSee('is-current-user', false)
            ->assertSee('Tu lugar');
    }

    public function test_guests_cannot_view_another_users_predictions(): void
    {
        $participant = User::factory()->create();

        $this->get(route('rankings.predicciones.show', $participant))
            ->assertRedirect('/login');
    }

    public function test_users_can_view_closed_matches_and_the_participants_predictions(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create([
            'name' => 'Ana',
            'username' => 'ana',
        ]);
        $closedMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(30));

        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $closedMatch->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Predicciones de Ana')
            ->assertSee('@ana')
            ->assertSee('Local 1 FC')
            ->assertSee('2 - 1');
    }

    public function test_closed_matches_are_ordered_from_newest_to_oldest(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $this->crearPartidoConFecha(now()->utc()->addMinutes(20));
        $this->crearPartidoConFecha(now()->utc()->addMinutes(40), 3, 4);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSeeInOrder(['Local 3 FC', 'Local 1 FC']);
    }

    public function test_users_can_view_their_own_predictions_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Perfil propio',
            'username' => 'perfil_propio',
        ]);

        $this->actingAs($user)
            ->get(route('rankings.predicciones.show', $user))
            ->assertOk()
            ->assertSee('Predicciones de Perfil propio')
            ->assertSee('@perfil_propio');
    }

    public function test_evaluated_predictions_show_if_the_user_scored_and_the_points_won(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $scoredMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(30));
        $missedMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(20), 3, 4);

        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $scoredMatch->id,
            'goles_local' => 2,
            'goles_visitante' => 1,
            'acertado' => false,
            'puntos' => 1,
        ]);
        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $missedMatch->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => 0,
        ]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Acert&oacute;', false)
            ->assertSee('1 punto')
            ->assertSee('No acert&oacute;', false)
            ->assertSee('0 puntos');
    }

    public function test_unevaluated_prediction_shows_pending_result(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $closedMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(30));

        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $closedMatch->id,
            'goles_local' => 1,
            'goles_visitante' => 1,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Pendiente de resultado')
            ->assertDontSee('No acert&oacute;', false);
    }

    public function test_prediction_cards_show_the_official_match_result(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $closedMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(30));
        $closedMatch->update([
            'goles_local' => 4,
            'goles_visitante' => 2,
        ]);

        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $closedMatch->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => 1,
        ]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Resultado final')
            ->assertSee('4 - 2')
            ->assertSee('Finalizado')
            ->assertSee('1 - 0');
    }

    public function test_prediction_cards_show_when_the_official_result_is_pending(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $this->crearPartidoConFecha(now()->utc()->addMinutes(30));

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Resultado final')
            ->assertSee('-- - --')
            ->assertSee('Pendiente');
    }

    public function test_open_matches_and_their_predictions_are_not_visible(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $openMatch = $this->crearPartidoConFecha(now()->utc()->addMinutes(61));

        Prediccion::create([
            'usuario_id' => $participant->id,
            'partido_id' => $openMatch->id,
            'goles_local' => 7,
            'goles_visitante' => 6,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertDontSee('Local 1 FC')
            ->assertDontSee('7 - 6')
            ->assertSee('Todav&iacute;a no hay partidos cerrados para mostrar.', false);
    }

    public function test_match_becomes_visible_exactly_at_the_sixty_minute_deadline(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $matchAtDeadline = $this->crearPartidoConFecha(now()->utc()->addMinutes(60));

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Local 1 FC')
            ->assertSee('Sin pron&oacute;stico', false);
    }

    public function test_all_closed_matches_are_visible_when_participant_did_not_predict(): void
    {
        Carbon::setTestNow('2026-06-07 12:00:00');

        $viewer = User::factory()->create();
        $participant = User::factory()->create();
        $this->crearPartidoConFecha(now()->utc()->addMinutes(20));

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $participant))
            ->assertOk()
            ->assertSee('Local 1 FC')
            ->assertSee('Sin pron&oacute;stico', false);
    }

    public function test_admin_predictions_profile_returns_not_found(): void
    {
        $viewer = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($viewer)
            ->get(route('rankings.predicciones.show', $admin))
            ->assertNotFound();
    }

    private function crearPartido(): Partido
    {
        return $this->crearPartidoConFecha(now()->utc()->addWeeks(3));
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
