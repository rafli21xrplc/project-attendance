<?php


namespace App\Traits;

use App\Models\permission;

trait PermissionTrait
{
        public function getPermission()
        {
                return permission::with(['student'])->paginate(10);
        }
}
