<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\religion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    private $religis = [
        'islam',
        'budha',
        'kristen',
        'katolik',
        'konghucu',
    ];

    private $roles = [
        'admin',
        'teacher',
        'student',
        'studentship',
        'treasurer',
    ];

    private $permissions = [
        'manage sistem',
        'manage payment',
        'manage report',
        'manage attendance',
    ];

    public function run(): void
    {

        foreach ($this->religis as $religi) {
            religion::create(['id' => Str::uuid(), 'name' => $religi]);
        }

        foreach ($this->roles as $role) {
            Role::create(['name' => $role]);
        }

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $rolePayment = Role::where('name', 'treasurer')->first();
        $permissionPayment = Permission::where('name', 'manage payment')->first();

        $rolePayment->givePermissionTo($permissionPayment);
        $permissionPayment->assignRole($rolePayment);

        $roleAdmin = Role::where('name', 'admin')->first();
        $permissionAdmin = Permission::where('name', 'manage sistem')->first();

        $roleAdmin->givePermissionTo($permissionAdmin);
        $permissionAdmin->assignRole($roleAdmin);

        User::create([
            'uuid' => Str::uuid(),
            'username' => '1234567899',
            'password' => Hash::make('password')
        ])->assignRole('admin');

        User::create([
            'uuid' => Str::uuid(),
            'username' => '12345678999',
            'password' => Hash::make('password')
        ])->assignRole('treasurer');

        // User::create([
        //     'uuid' => Str::uuid(),
        //     'name' => 'root',
        //     'email' => 'teacher@gmail.com',
        //     'password' => Hash::make('password')
        // ])->assignRole('teacher');

        // User::create([
        //     'uuid' => Str::uuid(),
        //     'name' => 'root',
        //     'email' => 'student@gmail.com',
        //     'password' => Hash::make('password')
        // ])->assignRole('student');

        // User::create([
        //     'uuid' => Str::uuid(),
        //     'name' => 'root',
        //     'email' => 'studentShip@gmail.com',
        //     'password' => Hash::make('password')
        // ])->assignRole('studentShip');
    }
}
