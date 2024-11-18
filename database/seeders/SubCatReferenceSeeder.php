<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SowReference;

class SubCatReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $subcat_ref = [
            [
                'parent_sow_sub_category_id' => '2',
                'sow_sub_category_id' => '3',
            ],
            [
                'parent_sow_sub_category_id' => '3',
                'sow_sub_category_id' => '4',
            ],
            [
                'parent_sow_sub_category_id' => '3',
                'sow_sub_category_id' => '5',
            ],
            [
                'parent_sow_sub_category_id' => '5',
                'sow_sub_category_id' => '6',
            ],
            [
                'parent_sow_sub_category_id' => '5',
                'sow_sub_category_id' => '7',
            ],
            [
                'parent_sow_sub_category_id' => '1',
                'sow_sub_category_id' => '9',
            ],
            [
                'parent_sow_sub_category_id' => null,
                'sow_sub_category_id' => '1',
            ],
            [
                'parent_sow_sub_category_id' => null,
                'sow_sub_category_id' => '2',
            ]
        ];

        SowReference::insert($subcat_ref);
    }
}
