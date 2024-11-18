<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DupaContent;

class DupaContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupaContent = [
            [
                'dupa_id' => '1',
            ],
            [
                'dupa_id' => '2',
            ],
            [
                'dupa_id' => '3',
            ],
            [
                'dupa_id' => '4',
            ],
            [
                'dupa_id' => '5',
            ],
            [
                'dupa_id' => '6',
            ],
            [
                'dupa_id' => '7',
            ],
            [
                'dupa_id' => '8',
            ],
            [
                'dupa_id' => '9',
            ],
            [
                'dupa_id' => '10',
            ],
            [
                'dupa_id' => '11',
            ],
            [
                'dupa_id' => '12',
            ],
            [
                'dupa_id' => '13',
            ],
            [
                'dupa_id' => '14',
            ],
            [
                'dupa_id' => '15',
            ],
        ];


        foreach($dupaContent as $dupaContents){
            DupaContent::create([
                'dupa_id' => $dupaContents['dupa_id'],
            ]);
        }
    }
}
