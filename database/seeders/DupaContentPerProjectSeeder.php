<?php

namespace Database\Seeders;

use App\Models\DupaContentPerProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DupaContentPerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupa_content_per_projects = [
            [
                'dupa_per_project_id' => '1',
            ],
            [
                'dupa_per_project_id' => '2',
            ],
            [
                'dupa_per_project_id' => '3',
            ],
        ];

        foreach ($dupa_content_per_projects as $dupa_content_per_project) {
            DupaContentPerProject::create([
                'dupa_per_project_id' => $dupa_content_per_project['dupa_per_project_id'],
            ]);
        }
    }
}
