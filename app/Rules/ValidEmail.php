<?php

namespace App\Rules;

use Closure;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('El campo :attribute debe ser un correo electronico valido.');

            return;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false
            || ! (new EmailValidator)->isValid($value, new NoRFCWarningsValidation)) {
            $fail('El campo :attribute debe ser un correo electronico valido.');
        }
    }
}
