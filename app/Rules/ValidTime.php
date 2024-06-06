<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTime implements Rule
{
    public function passes($attribute, $value)
    {
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return true;
        }
        
        try {
            $date = \DateTime::createFromFormat('H:i', $value);
            dd($value);
            if ($date && $date->format('H:i') === $value) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be a valid time format (HH:mm or HH:mm:ss).';
    }
}
