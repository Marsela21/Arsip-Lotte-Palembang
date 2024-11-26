<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1); // Gantilah '1' dengan ID pengguna yang ingin diberikan peran
        $user->assignRole('admin'); // Gantilah 'admin' dengan peran yang ingin diberikan
        Role::create(['name' => 'user']);
    }
}
