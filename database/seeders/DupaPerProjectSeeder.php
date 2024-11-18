<?php

namespace Database\Seeders;

use App\Models\DupaPerProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DupaPerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dupa_per_projects = [
            [
                'dupa_id' => '1',
                'b3_project_id' => '1',
                'sow_category_id' => '1',
                'subcategory_id' => '1',
                'dupa_per_project_group_id' => '1',
                'item_number' => '101(3)c1',
                'description' => 'Removal of Actual Structured Obstruction, 0.05m thick, Asphalt',
                'unit_id' => '4',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '40085.29',
                'output_per_hour' => '500.00',
            ],
            [
                'dupa_id' => '2',
                'b3_project_id' => '2',
                'sow_category_id' => '2',
                'subcategory_id' => '2',
                'dupa_per_project_group_id' => '2',
                'item_number' => '102(2)',
                'description' => 'Roadway Excavation',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '296.42',
                'output_per_hour' => '120.00',
            ],

            [
                'dupa_id' => '3',
                'b3_project_id' => '2',
                'sow_category_id' => '2',
                'subcategory_id' => '2',
                'dupa_per_project_group_id' => '2',
                'item_number' => '902 (1) a  ',
                'description' => 'Reinforcing Steel (Deformed) Grade 40',
                'unit_id' => '17',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '169.27',
                'output_per_hour' => '129.094',
            ],

        ];

        foreach ($dupa_per_projects as $dupa_per_project) {
            DupaPerProject::create([
                'dupa_id' => $dupa_per_project['dupa_id'],
                'b3_project_id' => $dupa_per_project['b3_project_id'],
                'sow_category_id' => $dupa_per_project['sow_category_id'],
                'subcategory_id' => $dupa_per_project['subcategory_id'],
                'dupa_per_project_group_id' => $dupa_per_project['dupa_per_project_group_id'],
                'item_number' => $dupa_per_project['item_number'],
                'description' => $dupa_per_project['description'],
                'unit_id' => $dupa_per_project['unit_id'],
                'category_dupa_id' => $dupa_per_project['category_dupa_id'],
                'direct_unit_cost' => $dupa_per_project['direct_unit_cost'],
                'output_per_hour' => $dupa_per_project['output_per_hour'],
            ]);
        }
    }
}
