<?php

namespace Database\Seeders;

use App\Models\DupaEquipmentPerProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DupaEquipmentPerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupa_equipment_per_projects = [
            [
                'dupa_content_per_project_id' => '1',
                'equipment_id' => '1',
                'no_of_unit' => '2',
                'no_of_hour' => '5',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'equipment_id' => '2',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'equipment_id' => '3',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],

            [
                'dupa_content_per_project_id' => '2',
                'equipment_id' => '2',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_per_project_id' => '2',
                'equipment_id' => '4',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],
            [
                'dupa_content_per_project_id' => '3',
                'equipment_id' => '7',
                'no_of_unit' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_per_project_id' => '3',
                'equipment_id' => '6',
                'no_of_unit' => '6',
                'no_of_hour' => '2',
            ],

        ];

        foreach ($dupa_equipment_per_projects as $dupa_equipment_per_project) {
            DupaEquipmentPerProject::create([
                'dupa_content_per_project_id' => $dupa_equipment_per_project['dupa_content_per_project_id'],
                'equipment_id' => $dupa_equipment_per_project['equipment_id'],
                'no_of_unit' => $dupa_equipment_per_project['no_of_unit'],
                'no_of_hour' => $dupa_equipment_per_project['no_of_hour'],
            ]);
        }
    }
}
