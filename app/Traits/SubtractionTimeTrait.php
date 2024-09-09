<?php


namespace App\Traits;

use App\Models\subtractionTime;
use Illuminate\Support\Str;


trait SubtractionTimeTrait
{

        public function storeOrUpdate(array $data)
        {

                $existingData = subtractionTime::first();

                return $existingData->update(
                        [
                                'tanggal' => $data['tanggal'],
                                'start_time' => $data['start_time'],
                                'end_time' => $data['end_time'],
                        ]
                );
        }
}
