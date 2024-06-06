<?php

namespace App\Traits;

use Faker\Provider\Uuid;

trait ObserverTrait
{
        public function generateId()
        {
                $min = pow(10, 8);
                $max = pow(10, 9) - 1;

                return mt_rand($min, $max);
        }

        public function generateUuid()
        {
                return Uuid::uuid();
        }
}
