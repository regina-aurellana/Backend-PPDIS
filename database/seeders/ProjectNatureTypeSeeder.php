<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProjectNatureType;

class ProjectNatureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $natureType = [
            [
                'project_nature_id' => '1',
                'name' => 'Road'
            ],
            [
                'project_nature_id' => '1',
                'name' => 'Waterways'
            ],
            [
                'project_nature_id' => '1',
                'name' => 'Swimming Pool'
            ],
            [
                'project_nature_id' => '1',
                'name' => 'Bridge'
            ],
            [
                'project_nature_id' => '2',
                'name' => 'Building'
            ],
            [
                'project_nature_id' => '2',
                'name' => 'Library'
            ],
            [
                'project_nature_id' => '2',
                'name' => 'Hospital'
            ],
            [
                'project_nature_id' => '2',
                'name' => 'School'
            ],
        ];

        foreach($natureType as $natureTypes){
            ProjectNatureType::create([
                'project_nature_id' => $natureTypes['project_nature_id'],
                'name' => $natureTypes['name'],
            ]);
        }
    }
}
