<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TableDupaComponent;

class TableComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $components = [
            [
                'dupa_id' => 1,
                'name'  => null,
            ],
            [
                'dupa_id' => 2,
                'name'  => null,
            ],
            [
                'dupa_id' => 3,
                'name'  => null,
            ],
            [
                'dupa_id' => 4,
                'name'  => null,
            ],
            [
                'dupa_id' => 5,
                'name'  => null,
            ],
            [
                'dupa_id' => 6,
                'name'  => null,
            ],
            [
                'dupa_id' => 7,
                'name'  => null,
            ],
            [
                'dupa_id' => 8,
                'name'  => null,
            ],
            [
                'dupa_id' => 9,
                'name'  => null,
            ],
            [
                'dupa_id' => 10,
                'name'  => null,
            ],
            [
                'dupa_id' => 11,
                'name'  => null,
            ],

            [
                'dupa_id' => 12,
                'name'  => null,
            ],

            [
                'dupa_id' => 13,
                'name'  => null,
            ],

            [
                'dupa_id' => 14,
                'name'  => null,
            ],

            [
                'dupa_id' => 15,
                'name'  => null,
            ],
            [
                'dupa_id' => 14,
                'name'  => "Lean Concrete",
            ],
            [
                'dupa_id' => 14,
                'name'  => "Wall Footing",
            ],
            [
                'dupa_id' => 14,
                'name'  => "Slab on fill",
            ],
            [
                'dupa_id' => 14,
                'name'  => "Stair Footing",
            ],
            [
                'dupa_id' => 14,
                'name'  => "Stairs",
            ],
            [
                'dupa_id' => 15,
                'name'  => "Column",
            ],
            [
                'dupa_id' => 15,
                'name'  => "Beam",
            ],
            [
                'dupa_id' => 15,
                'name'  => "Footing",
            ],
            [
                'dupa_id' => 15,
                'name'  => "Suspended Slab",
            ],



        ];


        foreach ($components as $component) {
            TableDupaComponent::create([
                'dupa_id' => $component['dupa_id'],
                'name' => $component['name'],
            ]);
        }
    }
}
