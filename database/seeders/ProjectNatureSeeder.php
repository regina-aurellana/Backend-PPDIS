<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProjectNature;

class ProjectNatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $projectNature = [
            [
                'name' => 'Horizontal',
            ],
            [
                'name' => 'Vertical',
            ],
        ];

        foreach($projectNature as $projectNatures){
            ProjectNature::create([
                'name' => $projectNatures['name'],
            ]);
        }
    }
}
