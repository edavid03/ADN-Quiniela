<?php

namespace Tests\Feature;

use App\Models\Regla;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReglaTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_rules(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/reglas')
            ->assertOk()
            ->assertSee('Reglas de la quiniela')
            ->assertSee('Marcador exacto');
    }

    public function test_admin_can_create_rule(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/reglas', [
                'titulo' => 'Premio especial',
                'contenido' => 'Se entregara al finalizar el torneo.',
            ])
            ->assertRedirect('/reglas')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('reglas', [
            'titulo' => 'Premio especial',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_admin_can_update_rule(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $regla = Regla::query()->firstOrFail();

        $this->actingAs($admin)
            ->patch("/admin/reglas/{$regla->id}", [
                'titulo' => 'Marcador exacto actualizado',
                'contenido' => 'Ahora contiene una explicacion mas clara.',
            ])
            ->assertRedirect('/reglas');

        $this->assertDatabaseHas('reglas', [
            'id' => $regla->id,
            'titulo' => 'Marcador exacto actualizado',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_manage_rules(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/reglas', [
                'titulo' => 'Regla no autorizada',
                'contenido' => 'No debe guardarse.',
            ])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('reglas', ['titulo' => 'Regla no autorizada']);
    }

    public function test_admin_can_delete_rule(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $regla = Regla::query()->firstOrFail();

        $this->actingAs($admin)
            ->delete("/admin/reglas/{$regla->id}")
            ->assertRedirect('/reglas');

        $this->assertDatabaseMissing('reglas', ['id' => $regla->id]);
    }
}
