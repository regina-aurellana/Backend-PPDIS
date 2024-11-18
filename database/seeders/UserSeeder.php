<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'IT Admin',
            // 'email' => 'it.admin@example.com',
            'username' => 'admin',
            'password' => bcrypt('password'), //password
            'position' => 'IT ADministrator',
            'is_active' => true,
            'role_id' => 1,
            'team_id' => null

        ]);

       User::create([
            'name' => 'Secretary Account',
            // 'email' => 'it.admin@example.com',
            'username' => 'secretary',
            'password' => bcrypt('password'), //password
            'position' => 'Secretary',
            'is_active' => true,
            'role_id' => 4,
            'team_id' => null

        ]);

    }
}
