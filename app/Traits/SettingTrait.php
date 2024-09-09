<?php

namespace App\Traits;

use App\Models\setting;
use App\Models\subtractionTime;

trait SettingTrait
{

        public function getSetting()
        {
                return setting::all();
        }

        public function getSubtractionTime()
        {
                return subtractionTime::first();
        }

        public function storeOrUpdateSetting(array $data)
        {
                foreach ($data as $key => $value) {
                        setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }

                return redirect()->back();
        }

        public function settingSpesialDay(array $data)
        {
                $specialDays[] = [
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                ];

                Setting::updateOrCreate(['key' => 'spesial_day'], ['value' => json_encode($specialDays)]);

                return redirect()->back();
        }
}
