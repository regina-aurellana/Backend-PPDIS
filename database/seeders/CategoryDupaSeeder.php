<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\CategoryDupa;

class CategoryDupaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dupa_cat = [
            [
                'name' => 'Horizontal',
            ],
            [
                'name' => 'Vertical',
            ],
            [
                'name' => 'Common Items',
            ],
        ];

        foreach ($dupa_cat as $dupa_cats) {
            CategoryDupa::create([
                'name' => $dupa_cats['name'],
            ]);
        }
    }
}
