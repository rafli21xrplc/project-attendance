<?php


namespace App\Traits;

use App\Models\permission;

trait PermissionTrait
{
        public function getPermission()
        {
            return permission::with(['student'])->orderBy('created_at', 'desc')->get();
        }
}
