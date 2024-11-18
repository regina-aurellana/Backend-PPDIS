<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TakeOffTableFieldsInput;

class TakeOffTableFieldInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $take_off_table_fields_inputs = [
            [
                'row_no' => '1',
                'take_off_table_field_id' => '1',
                'value' => '22'
            ],
            [
                'row_no' => '1',
                'take_off_table_field_id' => '2',
                'value' => '25'
            ],
            [
                'row_no' => '1',
                'take_off_table_field_id' => '3',
                'value' => '89'
            ],
            [
                'row_no' => '1',
                'take_off_table_field_id' => '4',
                'value' => '50'
            ],
        ];

        TakeOffTableFieldsInput::insert($take_off_table_fields_inputs);
    }
}
