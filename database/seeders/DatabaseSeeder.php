<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    private $roles = [
        'admin',
        'petugas piket',
        'kesiswaan',
        'koordinator',
        'kepala sekolah',
    ];

    private $permissions = [
        'manage sistem',
        'manage laporan',
    ];

    public function run(): void
    {

        foreach ($this->roles as $role) {
            Role::create(['name' => $role]);
        }

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::where('name', 'admin')->first();
        $permission = Permission::where('name', 'manage sistem')->first();

        $role->givePermissionTo($permission);
        $permission->assignRole($role);

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Surya Rafliansyah',
            'email' => 'untukprojects123@gmail.com',
            'password' => Hash::make('password')
        ])->assignRole('admin');

    }
}
