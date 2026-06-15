<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class AdminAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_access_audit_history(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->get('/admin/auditoria')->assertRedirect('/login');
        $this->actingAs($user)->get('/admin/auditoria')->assertRedirect('/dashboard');
        $this->actingAs($admin)
            ->get('/admin/auditoria')
            ->assertOk()
            ->assertSee('Historial de auditor&iacute;a', false);
    }

    public function test_model_creations_updates_and_deletions_are_audited(): void
    {
        $user = User::factory()->create();

        $created = Auditoria::query()
            ->where('table_name', 'users')
            ->where('record_id', $user->id)
            ->where('action', 'created')
            ->firstOrFail();

        $this->assertSame('sistema', $created->actor_type);
        $this->assertSame($user->email, $created->new_values['email']);
        $this->assertArrayNotHasKey('password', $created->new_values);
        $this->assertArrayNotHasKey('remember_token', $created->new_values);

        $user->update(['name' => 'Nombre cambiado']);

        $updated = Auditoria::query()
            ->where('table_name', 'users')
            ->where('record_id', $user->id)
            ->where('action', 'updated')
            ->firstOrFail();

        $this->assertSame(['name' => $created->new_values['name']], $updated->old_values);
        $this->assertSame(['name' => 'Nombre cambiado'], $updated->new_values);

        $user->delete();

        $deleted = Auditoria::query()
            ->where('table_name', 'users')
            ->where('record_id', $user->id)
            ->where('action', 'deleted')
            ->firstOrFail();

        $this->assertSame('Nombre cambiado', $deleted->old_values['name']);
        $this->assertNull($deleted->new_values);
        $this->assertArrayNotHasKey('password', $deleted->old_values);
        $this->assertArrayNotHasKey('admin_key', $deleted->old_values);
    }

    public function test_public_registration_is_audited_as_guest_with_request_context(): void
    {
        $this->withHeader('User-Agent', 'Audit Test Browser')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post('/register', [
                'name' => 'Usuario Invitado',
                'username' => 'usuario_invitado',
                'cedula' => '23456789',
                'email' => 'invitado@example.com',
                'password' => 'clave-segura',
                'password_confirmation' => 'clave-segura',
            ])
            ->assertRedirect('/login');

        $audit = Auditoria::query()
            ->where('table_name', 'users')
            ->where('action', 'created')
            ->whereJsonContains('new_values->username', 'usuario_invitado')
            ->firstOrFail();

        $this->assertSame('invitado', $audit->actor_type);
        $this->assertSame('203.0.113.10', $audit->ip_address);
        $this->assertSame('Audit Test Browser', $audit->user_agent);
        $this->assertArrayNotHasKey('password', $audit->new_values);
    }

    public function test_authenticated_http_action_records_actor_and_result_recalculations(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
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
            ->withHeader('User-Agent', 'Admin Audit Browser')
            ->post('/admin/resultados', [
                'resultados' => [
                    $partido->id => ['goles_local' => 2, 'goles_visitante' => 1],
                ],
            ])
            ->assertRedirect('/admin/resultados');

        $matchAudit = Auditoria::query()
            ->where('actor_id', $admin->id)
            ->where('table_name', 'partidos')
            ->where('action', 'updated')
            ->firstOrFail();
        $predictionAudit = Auditoria::query()
            ->where('actor_id', $admin->id)
            ->where('table_name', 'predicciones')
            ->where('action', 'updated')
            ->firstOrFail();

        $this->assertSame($admin->username, $matchAudit->actor_name);
        $this->assertSame('usuario', $matchAudit->actor_type);
        $this->assertSame('Admin Audit Browser', $matchAudit->user_agent);
        $this->assertSame(['goles_local' => null, 'goles_visitante' => null], $matchAudit->old_values);
        $this->assertSame(['goles_local' => 2, 'goles_visitante' => 1], $matchAudit->new_values);
        $this->assertSame(['acertado' => 0, 'puntos' => null], $predictionAudit->old_values);
        $this->assertSame(['acertado' => true, 'puntos' => 3], $predictionAudit->new_values);
    }

    public function test_result_recalculation_only_audits_real_prediction_changes_for_the_updated_match(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $partidoActualizado = $this->crearPartido();
        $otroPartido = $this->crearPartido(3, 4);

        $prediccionEvaluada = Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partidoActualizado->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);
        $prediccionOtroPartido = Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $otroPartido->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($admin)
            ->post('/admin/resultados', [
                'resultados' => [
                    $partidoActualizado->id => ['goles_local' => 2, 'goles_visitante' => 1],
                    $otroPartido->id => ['goles_local' => null, 'goles_visitante' => null],
                ],
            ])
            ->assertRedirect('/admin/resultados');

        $auditoriasPredicciones = Auditoria::query()
            ->where('actor_id', $admin->id)
            ->where('table_name', 'predicciones')
            ->where('action', 'updated')
            ->get();

        $this->assertCount(1, $auditoriasPredicciones);
        $this->assertSame((string) $prediccionEvaluada->id, $auditoriasPredicciones->sole()->record_id);
        $this->assertSame(['puntos' => null], $auditoriasPredicciones->sole()->old_values);
        $this->assertSame(['puntos' => 0], $auditoriasPredicciones->sole()->new_values);
        $this->assertDatabaseHas('predicciones', [
            'id' => $prediccionOtroPartido->id,
            'puntos' => null,
            'acertado' => false,
        ]);
    }

    public function test_rolled_back_transaction_does_not_leave_audit_records(): void
    {
        try {
            DB::transaction(function (): void {
                Equipo::create([
                    'id' => 99,
                    'name' => 'Equipo temporal',
                    'code' => 'TMP',
                    'grupo' => 'A',
                ]);

                throw new RuntimeException('Rollback');
            });
        } catch (RuntimeException) {
            //
        }

        $this->assertDatabaseMissing('equipos', ['id' => 99]);
        $this->assertDatabaseMissing('auditorias', [
            'table_name' => 'equipos',
            'record_id' => '99',
        ]);
    }

    public function test_admin_can_filter_audit_history(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Equipo::create(['id' => 10, 'name' => 'Visible', 'code' => 'VIS', 'grupo' => 'A']);
        Auditoria::create([
            'actor_type' => 'sistema',
            'action' => 'created',
            'table_name' => 'partidos',
            'record_id' => '999',
            'new_values' => ['name' => 'No debe mostrarse'],
        ]);

        $this->actingAs($admin)
            ->get('/admin/auditoria?action=created&table_name=equipos')
            ->assertOk()
            ->assertSee('Visible')
            ->assertDontSee('No debe mostrarse');
    }

    private function crearPartido(int $localId = 1, int $visitanteId = 2): Partido
    {
        $local = Equipo::create(['id' => $localId, 'name' => 'Local', 'code' => 'LOC', 'grupo' => 'A']);
        $visitante = Equipo::create(['id' => $visitanteId, 'name' => 'Visitante', 'code' => 'VIS', 'grupo' => 'A']);

        return Partido::create([
            'local_id' => $local->id,
            'visitante_id' => $visitante->id,
            'fecha_utc' => now()->utc()->addWeeks(3)->format('Y-m-d H:i:s'),
            'estadio' => 'Estadio',
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ]);
    }
}
