<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Prediccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_access_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/usuarios')
            ->assertRedirect('/dashboard');
    }

    public function test_non_admin_cannot_create_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/usuarios', [
                'name' => 'Usuario Manual',
                'username' => 'usuario_manual',
                'cedula' => '23456789',
                'email' => 'manual@example.com',
                'password' => 'clave-segura',
                'password_confirmation' => 'clave-segura',
            ])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('users', ['username' => 'usuario_manual']);
    }

    public function test_admin_can_view_user_management(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/usuarios')
            ->assertOk()
            ->assertSee('Gesti&oacute;n de usuarios', false);
    }

    public function test_admin_can_create_approved_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/usuarios', [
                'name' => 'Usuario Manual',
                'username' => 'usuario_manual',
                'cedula' => '23456789',
                'email' => 'manual@example.com',
                'password' => 'clave-segura',
                'password_confirmation' => 'clave-segura',
            ])
            ->assertRedirect('/admin/usuarios');

        $user = User::query()->where('username', 'usuario_manual')->firstOrFail();

        $this->assertTrue($user->isApproved());
    }

    public function test_admin_can_approve_pending_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pendingUser = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch("/admin/usuarios/{$pendingUser->id}/aceptar")
            ->assertRedirect('/admin/usuarios');

        $this->assertTrue($pendingUser->fresh()->isApproved());
    }

    public function test_admin_can_delete_user_without_predictions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/usuarios/{$user->id}")
            ->assertRedirect('/admin/usuarios');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_itself(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete("/admin/usuarios/{$admin->id}")
            ->assertSessionHas('security_alert');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_user_with_predictions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $partido = $this->crearPartido();

        Prediccion::create([
            'usuario_id' => $user->id,
            'partido_id' => $partido->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'acertado' => false,
            'puntos' => null,
        ]);

        $this->actingAs($admin)
            ->delete("/admin/usuarios/{$user->id}")
            ->assertSessionHas('security_alert');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
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
