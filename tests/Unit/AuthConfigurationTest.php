<?php

namespace Tests\Unit;

use Tests\TestCase;

class AuthConfigurationTest extends TestCase
{
    public function test_default_authentication_guard_uses_server_side_sessions(): void
    {
        $this->assertSame('web', config('auth.defaults.guard'));
        $this->assertSame('session', config('auth.guards.web.driver'));
        $this->assertSame('users', config('auth.guards.web.provider'));
    }

    public function test_no_jwt_authentication_guard_is_configured(): void
    {
        $jwtGuards = collect(config('auth.guards'))
            ->filter(fn (array $guard): bool => ($guard['driver'] ?? null) === 'jwt');

        $this->assertEmpty(
            $jwtGuards,
            'JWT was unexpectedly configured. Review the login and authorization tests before changing the authentication contract.',
        );
    }
}
