<?php

namespace Database\Seeders;

use App\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $it_admin = Role::create([
            'name' => 'It Admin'
        ]);

        $city_admin = Role::create([
            'name' => 'City Admin'
        ]);

        $deputy = Role::create([
            'name' => 'Deputy'
        ]);

        $secretary = Role::create([
            'name' => 'Secretary'
        ]);

        $chief_admin = Role::create([
            'name' => 'Chief Admin'
        ]);

        $team_1 = Role::create([
            'name' => 'Engineer',
        ]);

        // $team_2 = Role::create([
        //     'name' => 'team',
        //     'team_number' => '2'
        // ]);


    }
}
