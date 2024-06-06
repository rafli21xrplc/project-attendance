<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class EmailExists implements Rule
{
    // /**
    //  * Run the validation rule.
    //  *
    //  * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
    //  */
    // public function validate(string $attribute, mixed $value, Closure $fail): void
    // {
    //     //
    // }

    public function passes($attribute, $value)
    {
        return DB::table('users')->where('email', $value)->exists();
    }

    public function message()
    {
        return 'Email tidak terdaftar.';
    }
}
