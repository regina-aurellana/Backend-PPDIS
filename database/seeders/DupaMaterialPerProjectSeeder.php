<?php

namespace Database\Seeders;

use App\Models\DupaMaterialPerProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DupaMaterialPerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupa_material_per_projects = [
            [
                'dupa_content_per_project_id' => '1',
                'material_id' => '1',
                'quantity' => '2',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'material_id' => '5',
                'quantity' => '2',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'material_id' => '7',
                'quantity' => '1',
            ],
            [
                'dupa_content_per_project_id' => '1',
                'material_id' => '8',
                'quantity' => '3',
            ],
        ];

        foreach ($dupa_material_per_projects as $dupa_material_per_project) {
            DupaMaterialPerProject::create([
                'dupa_content_per_project_id' => $dupa_material_per_project['dupa_content_per_project_id'],
                'material_id' => $dupa_material_per_project['material_id'],
                'quantity' => $dupa_material_per_project['quantity'],
            ]);
        }
    }
}
