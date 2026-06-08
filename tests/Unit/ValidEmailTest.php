<?php

namespace Tests\Unit;

use App\Rules\ValidEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidEmailTest extends TestCase
{
    #[DataProvider('validEmails')]
    public function test_it_accepts_valid_email_addresses(string $email): void
    {
        $errors = $this->validate($email);

        $this->assertSame([], $errors);
    }

    #[DataProvider('invalidEmails')]
    public function test_it_rejects_invalid_email_addresses(mixed $email): void
    {
        $errors = $this->validate($email);

        $this->assertCount(1, $errors);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validEmails(): array
    {
        return [
            'standard address' => ['usuario@example.com'],
            'subdomain' => ['usuario@correo.example.com'],
            'plus alias' => ['usuario+quiniela@example.com'],
        ];
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function invalidEmails(): array
    {
        return [
            'missing at sign' => ['usuario.example.com'],
            'missing local part' => ['@example.com'],
            'missing domain' => ['usuario@'],
            'domain without suffix' => ['usuario@localhost'],
            'consecutive dots' => ['usuario..quiniela@example.com'],
            'leading whitespace' => [' usuario@example.com'],
            'non-string value' => [12345],
        ];
    }

    /**
     * @return list<string>
     */
    private function validate(mixed $email): array
    {
        $errors = [];

        (new ValidEmail)->validate('email', $email, function (string $message) use (&$errors): void {
            $errors[] = $message;
        });

        return $errors;
    }
}
