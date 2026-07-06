<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotReservedUsername implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $username = strtolower((string) $value);

        $reserved = array_map('strtolower', config('biotree.reserved_usernames', []));
        if (in_array($username, $reserved, true)) {
            $fail('That username isn\'t available.');

            return;
        }

        $premium = array_map('strtolower', config('biotree.premium_usernames', []));
        if (in_array($username, $premium, true)) {
            $fail('This is a premium username — available to buy from the admin.');
        }
    }
}
