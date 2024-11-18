<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DupaPerProjectGroup;

class DupaPerProjectGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $group = [
            [
                'b3_project_id' => '1',
                'group_no' => '1',
                'created_at' => now(),
            ],
            [
                'b3_project_id' => '2',
                'group_no' => '1',
                'created_at' => now(),
            ],
            [
                'b3_project_id' => '3',
                'group_no' => '1',
                'created_at' => now(),
            ],
            [
                'b3_project_id' => '4',
                'group_no' => '1',
                'created_at' => now(),
            ],

        ];

        DupaPerProjectGroup::insert($group);
    }
}
