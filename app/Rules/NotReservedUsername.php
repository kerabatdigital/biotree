<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotReservedUsername implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $reserved = array_map('strtolower', config('biotree.reserved_usernames', []));

        if (in_array(strtolower((string) $value), $reserved, true)) {
            $fail('That username is reserved. Please choose another.');
        }
    }
}
