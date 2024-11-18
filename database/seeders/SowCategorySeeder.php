<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SowCategory;

class SowCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $sow_cat = [
            [
                'item_code' => 'Part I',
                'name' => 'General Requirements'
            ],
            [
                'item_code' => 'Part II',
                'name' => 'Other General Requirements'
            ],
            [
                'item_code' => 'Part III',
                'name' => 'Civil, Mechanical, Electrical and Sanitary/Plumbing Works'
            ],
        ];

        foreach($sow_cat as $sow_cats){
            SowCategory::create([
                'item_code' => $sow_cats['item_code'],
                'name' => $sow_cats['name'],
            ]);
        }
    }
}
