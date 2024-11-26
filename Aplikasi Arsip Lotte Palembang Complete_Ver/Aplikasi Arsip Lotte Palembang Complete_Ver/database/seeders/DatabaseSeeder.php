<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Membuat peran admin jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Membuat user admin1 dan memberikan peran admin
        $user1 = User::create([
            'name' => 'Admin1',
            'email' => 'admin1@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        $user1->assignRole($adminRole);

        // Membuat user admin2 dan memberikan peran admin
        $user2 = User::create([
            'name' => 'Admin2',
            'email' => 'admin2@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        $user2->assignRole($adminRole);

        // Membuat user admin3 dan memberikan peran admin
        $user3 = User::create([
            'name' => 'Admin3',
            'email' => 'admin3@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        $user3->assignRole($adminRole);
    }
}
