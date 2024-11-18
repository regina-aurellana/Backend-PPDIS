<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DupaEquipment;

class DupaEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupaEquipment = [
            [
                'dupa_content_id' => '1',
                'equipment_id' => '1',
                'no_of_unit' => '2',
                'no_of_hour' => '5',
            ],
            [
                'dupa_content_id' => '1',
                'equipment_id' => '2',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_id' => '1',
                'equipment_id' => '3',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],

            [
                'dupa_content_id' => '2',
                'equipment_id' => '2',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_id' => '2',
                'equipment_id' => '4',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],
            [
                'dupa_content_id' => '3',
                'equipment_id' => '7',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_id' => '3',
                'equipment_id' => '6',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],
            [
                'dupa_content_id' => '7',
                'equipment_id' => '6',
                'no_of_unit' => '2',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '7',
                'equipment_id' => '18',
                'no_of_unit' => '2',
                'no_of_hour' => '0.50',
            ],
            [
                'dupa_content_id' => '7',
                'equipment_id' => '19',
                'no_of_unit' => '2',
                'no_of_hour' => '0.50',
            ],
            [
                'dupa_content_id' => '9',
                'equipment_id' => '4',
                'no_of_unit' => '1',
                'no_of_hour' => '1',
            ],

            [
                'dupa_content_id' => '9',
                'equipment_id' => '6',
                'no_of_unit' => '2',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '11',
                'equipment_id' => '20',
                'no_of_unit' => '1',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '11',
                'equipment_id' => '8',
                'no_of_unit' => '1',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '11',
                'equipment_id' => '5',
                'no_of_unit' => '1',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '12',
                'equipment_id' => '21',
                'no_of_unit' => '1',
                'no_of_hour' => '1',
            ],

            [
                'dupa_content_id' => '12',
                'equipment_id' => '22',
                'no_of_unit' => '1',
                'no_of_hour' => '0.25',
            ],
            [
                'dupa_content_id' => '13',
                'equipment_id' => '8',
                'no_of_unit' => '1',
                'no_of_hour' => '0.50',
            ],
            [
                'dupa_content_id' => '14',
                'equipment_id' => '10',
                'no_of_unit' => '2',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '14',
                'equipment_id' => '9',
                'no_of_unit' => '1',
                'no_of_hour' => '0.27',
            ],
            [
                'dupa_content_id' => '15',
                'equipment_id' => '10',
                'no_of_unit' => '2',
                'no_of_hour' => '1',
            ],
            [
                'dupa_content_id' => '15',
                'equipment_id' => '9',
                'no_of_unit' => '1',
                'no_of_hour' => '0.27',
            ],



        ];

        foreach($dupaEquipment as $dupaEquipments){
            DupaEquipment::create([
                'dupa_content_id' => $dupaEquipments['dupa_content_id'],
                'equipment_id' => $dupaEquipments['equipment_id'],
                'no_of_unit' => $dupaEquipments['no_of_unit'],
                'no_of_hour' => $dupaEquipments['no_of_hour'],
            ]);
        }
    }
}
