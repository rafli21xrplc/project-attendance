<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTime implements Rule
{
    public function passes($attribute, $value)
    {
        // Check for both formats: HH:mm and HH:mm:ss
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            try {
                // Handle time with optional seconds
                $date = \DateTime::createFromFormat('H:i:s', $value) ?: \DateTime::createFromFormat('H:i', $value);

                // Ensure the time format matches the input exactly
                if ($date && ($date->format('H:i') === $value || $date->format('H:i:s') === $value)) {
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be a valid time format (HH:mm or HH:mm:ss).';
    }
}
