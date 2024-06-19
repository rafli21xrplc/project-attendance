<?php

namespace App\Traits;

use App\Models\setting;

trait SettingTrait
{

        public function getSetting()
        {
                return setting::all();
        }

        public function storeOrUpdateSetting(array $data)
        {
                foreach ($data as $key => $value) {
                        setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }

                return redirect()->back();
        }
}
