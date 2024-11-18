<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  App\Models\TakeOffTable;

class TakeOffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $take_off_table = [

            [
                'take_off_id' => '1',
                'sow_category_id' => '2',
                'table_dupa_component_formula_id' => '1',
                'contingency' => null,
                'table_say' => null,
            ],

        ];

        TakeOffTable::insert($take_off_table);
    }
}
