<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DupaCategory;

class DupaCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dupa_cat = [
            [
                'dupa_id' => '1',
                'nature_id' => '1',
            ],
            [
                'dupa_id' => '2',
                'nature_id' => '2',
            ],
            [
                'dupa_id' => '2',
                'nature_id' => '1',
            ],
        ];

        foreach ($dupa_cat as $dupa_cats) {
            DupaCategory::create([
                'dupa_id' => $dupa_cats['dupa_id'],
                'nature_id' => $dupa_cats['nature_id']
            ]);
        }
    }
}
