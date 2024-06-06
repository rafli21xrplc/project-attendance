<?php

namespace App\Contracts\Repositories;

class ObserverRepository
{
    protected function generateRandomMath()
    {
        $min = pow(10, 8);
        $max = pow(10, 9) - 1;

        return mt_rand($min, $max);
    }
}
