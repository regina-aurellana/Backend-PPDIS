<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SowSubCategory;

class SowSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $subcat = [
            [
                'item_code' => 'CSW01',
                'name' => 'Earthworks',
                'sow_category_id' => '1',
            ],
            [
                'item_code' => 'OGR03',
                'name' => 'Temporary Facilities (including bunkhouse, sanitation facilities, electric meter/sub-meter, water meter/sub-meter, etc.)',
                'sow_category_id' => '2',
            ],
            [
                'item_code' => 'OGR0301',
                'name' => 'electric meter/sub-meter, water meter/sub-meter, etc.',
                'sow_category_id' => '1',
            ],
            [
                'item_code' => 'OGR030101',
                'name' => 'Temporary Facilities ',
                'sow_category_id' => '1',
            ],
            [
                'item_code' => 'OGR030102',
                'name' => 'Temporary Facilities 2',
                'sow_category_id' => '1',
            ],
            [
                'item_code' => 'OGR03010201',
                'name' => 'Temporary Facilities 2-1',
                'sow_category_id' => '1',
            ],
            [
                'item_code' => 'OGR03010202',
                'name' => 'Temporary Facilities 2-2',
                'sow_category_id' => '2',
            ],
            [
                'item_code' => 'AW01',
                'name' => 'Temporary',
                'sow_category_id' => '2',
            ],
            [
                'item_code' => 'CSW0101',
                'name' => 'Facilities ',
                'sow_category_id' => '2',
            ],
        ];

        // SowSubCategory::insert($subcat);

        foreach($subcat as $subcats){
            SowSubCategory::create([
                'item_code' => $subcats['item_code'],
                'name' => $subcats['name'],
                'sow_category_id' => $subcats['sow_category_id'],
            ]);
        }
    }
}
