<?php

namespace Database\Seeders;

use App\Models\DupaLaborPerProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DupaLaborPerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupa_labor_per_projects = [
            [
                'dupa_content_per_project_id' => '1',
                'labor_id' => '1',
                'no_of_person' => '2',
                'no_of_hour' => '5',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'labor_id' => '2',
                'no_of_person' => '1',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_per_project_id' => '2',
                'labor_id' => '1',
                'no_of_person' => '5',
                'no_of_hour' => '4',
            ],
            [
                'dupa_content_per_project_id' => '2',
                'labor_id' => '2',
                'no_of_person' => '2',
                'no_of_hour' => '3',
            ],

            [
                'dupa_content_per_project_id' => '3',
                'labor_id' => '1',
                'no_of_person' => '5',
                'no_of_hour' => '4',
            ],
            [
                'dupa_content_per_project_id' => '3',
                'labor_id' => '2',
                'no_of_person' => '2',
                'no_of_hour' => '3',
            ],
            [
                'dupa_content_per_project_id' => '3',
                'labor_id' => '3',
                'no_of_person' => '2',
                'no_of_hour' => '3',
            ],
        ];

        foreach ($dupa_labor_per_projects as $dupa_labor_per_project) {
            DupaLaborPerProject::create([
                'dupa_content_per_project_id' => $dupa_labor_per_project['dupa_content_per_project_id'],
                'labor_id' => $dupa_labor_per_project['labor_id'],
                'no_of_person' => $dupa_labor_per_project['no_of_person'],
                'no_of_hour' => $dupa_labor_per_project['no_of_hour'],
            ]);
        }
    }
}
