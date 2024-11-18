<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Labor;

class LaborSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $labor = [
            [	
                'item_code' => '101',
                'designation' => 'Construction Foreman',
                'hourly_rate' => 157.23,
            ],
            [	
                'item_code' => '102',
                'designation' => 'Skilled Laborer',
                'hourly_rate' => 114.31,
            ],
            [	
                'item_code' => '103',
                'designation' => 'Unskilled Laborer',
                'hourly_rate' => 88.08,
            ],
            [	
                'item_code' => '104',
                'designation' => 'First Aider',
                'hourly_rate' => 88.08,
            ],
        ];

        foreach($labor as $labors){
            Labor::create([
                'item_code' => $labors['item_code'],
                'designation' => $labors['designation'],
                'hourly_rate' => $labors['hourly_rate'],
            ]);
        }
    }
}
