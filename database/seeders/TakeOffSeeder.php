<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TakeOff;

class TakeOffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $take_off = [
            [
                'b3_project_id' => "1",
                'dupa_per_project_group_id' => "1",
                'limit' => "STA 0+000 (NARIG STREET) TO STA 0+224 (ANAHAW STREET)",
                'length' => "0.224 KM",
            ],
            [
                'b3_project_id' => "2",
                'dupa_per_project_group_id' => "2",
                'limit' => "STA 0+000 (ARAMISMIS STREET) TO STA 0+162 (NARIG STREET)",
                'length' => "0.206 KM",
            ],
        ];

        TakeOff::insert($take_off);


    }
}
