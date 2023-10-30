<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'          => 'Administrator',
            'email'         => 'admin@gmail.com',
            'password'      => bcrypt('password')
        ]);

        //assign permission to role
        $role = Role::find(1);
        $permissions = Permission::all();

        $role->syncPermissions($permissions);

        $user               = User::find(1);
        $user->assignRole($role->name);
    }
}
