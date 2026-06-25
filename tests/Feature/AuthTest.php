<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\TokenMismatchException;
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

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Registra tus datos');
    }

    public function test_users_can_register_and_remain_pending(): void
    {
        $this->post('/register', [
            'name' => 'Usuario Pendiente',
            'username' => 'usuario_pendiente',
            'cedula' => '12345678',
            'email' => 'pendiente@example.com',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ])->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'username' => 'usuario_pendiente',
            'cedula' => '12345678',
            'approved_at' => null,
        ]);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_cedula(): void
    {
        User::factory()->create(['cedula' => '12345678']);

        $this->post('/register', [
            'name' => 'Usuario Duplicado',
            'username' => 'usuario_duplicado',
            'cedula' => '12345678',
            'email' => 'duplicado@example.com',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ])->assertSessionHasErrors('cedula');
    }

    public function test_registration_rejects_invalid_cedula(): void
    {
        $this->post('/register', [
            'name' => 'Usuario Invalido',
            'username' => 'usuario_invalido',
            'cedula' => 'V-12345678',
            'email' => 'invalido@example.com',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ])->assertSessionHasErrors('cedula');
    }

    public function test_registration_rejects_invalid_email(): void
    {
        $this->post('/register', [
            'name' => 'Usuario Invalido',
            'username' => 'usuario_invalido',
            'cedula' => '12345678',
            'email' => 'usuario@localhost',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['username' => 'usuario_invalido']);
    }

    public function test_registration_rejects_duplicate_username_and_email(): void
    {
        User::factory()->create([
            'username' => 'usuario_existente',
            'email' => 'existente@example.com',
        ]);

        $this->post('/register', [
            'name' => 'Usuario Duplicado',
            'username' => 'usuario_existente',
            'cedula' => '12345678',
            'email' => 'existente@example.com',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ])->assertSessionHasErrors(['username', 'email']);
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

    public function test_expired_login_token_redirects_back_to_login_with_message(): void
    {
        Route::post('/csrf-expired-test', function (): void {
            throw new TokenMismatchException;
        });

        $this->from('/login')
            ->post('/csrf-expired-test', [
                'username' => 'usuario_prueba',
                'password' => 'clave-segura',
                '_token' => 'token-vencido',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('session')
            ->assertSessionHasInput('username', 'usuario_prueba')
            ->assertSessionMissing('_old_input.password');
    }

    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'usuario_prueba',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Mesa de la quiniela')
            ->assertSee('Crear o editar pronosticos')
            ->assertSee('Mis pronosticos')
            ->assertSee('Cierre de pronosticos')
            ->assertSee('href="'.route('pronosticos.edit').'"', false);
    }

    public function test_admin_users_see_admin_dashboard_link(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard admin')
            ->assertDontSee('Crear o editar pronosticos')
            ->assertDontSee('Mis pronosticos')
            ->assertDontSee('Cierre de pronosticos')
            ->assertDontSee('href="/pronosticos"', false);
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

    public function test_pending_users_cannot_login(): void
    {
        User::factory()->pending()->create([
            'username' => 'usuario_pendiente',
            'password' => Hash::make('clave-segura'),
        ]);

        $this->post('/login', [
            'username' => 'usuario_pendiente',
            'password' => 'clave-segura',
        ])
            ->assertSessionHasErrors('username');

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
