<?php

namespace App\Rules\auth;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;
use App\Helper\Message;

class ValidRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = User::where('mobile', $value)->whereIn('role_id', [1,2])
                    ->exists();
        if (!$exists) {
            $fail(sprintf(Message::ROLE_VALIDATION,$value));
        }
    }
}
