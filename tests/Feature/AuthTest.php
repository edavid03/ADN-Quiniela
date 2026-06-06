<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Usuario');
    }

    public function test_users_can_login_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'username' => 'usuario_prueba',
            'password' => Hash::make('clave-segura'),
        ]);

        $this->post('/login', [
            'username' => 'usuario_prueba',
            'password' => 'clave-segura',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'usuario_prueba',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Mesa de la quiniela');
    }

    public function test_admin_users_see_admin_dashboard_link(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard admin');
    }

    public function test_users_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'username' => 'usuario_prueba',
            'password' => Hash::make('clave-segura'),
        ]);

        $this->post('/login', [
            'username' => 'usuario_prueba',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
