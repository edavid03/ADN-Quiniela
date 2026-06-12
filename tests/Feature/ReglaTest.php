<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReglaTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_static_rules(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/reglas')
            ->assertOk()
            ->assertSee('Reglas de la quiniela')
            ->assertSee('Marcador exacto: 3 puntos')
            ->assertSee('Resultado correcto: 1 punto')
            ->assertSee('Sin acierto: 0 puntos');
    }

    public function test_rules_page_does_not_require_a_rules_database_table(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/reglas')
            ->assertOk()
            ->assertDontSee('migrate --force');
    }
}
