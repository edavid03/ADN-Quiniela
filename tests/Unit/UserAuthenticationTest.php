<?php

namespace Tests\Unit;

use App\Models\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserAuthenticationTest extends TestCase
{
    public function test_user_is_approved_when_approved_at_is_present(): void
    {
        $user = new User;
        $user->setRawAttributes(['approved_at' => new DateTimeImmutable]);

        $this->assertTrue($user->isApproved());
    }

    public function test_user_is_pending_when_approved_at_is_missing(): void
    {
        $user = new User;
        $user->setRawAttributes(['approved_at' => null]);

        $this->assertFalse($user->isApproved());
    }

    public function test_sensitive_authentication_fields_are_hidden_from_serialization(): void
    {
        $user = new User;
        $user->setRawAttributes([
            'username' => 'usuario_prueba',
            'password' => 'clave-segura',
            'remember_token' => 'token-recordarme',
        ]);

        $serialized = $user->toArray();

        $this->assertArrayNotHasKey('password', $serialized);
        $this->assertArrayNotHasKey('remember_token', $serialized);
    }
}
