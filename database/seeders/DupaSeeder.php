<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Dupa;

class DupaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupa = [
            [
                'subcategory_id' => '1',
                'item_number' => '101(3)c1',
                'description' => 'Removal of Actual Structured Obstruction, 0.05m thick, Asphalt',
                'unit_id' => '4',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '40085.29',
                'output_per_hour' => '500.00',
            ],
            [

                'subcategory_id' => '2',
                'item_number' => '102(2)',
                'description' => 'Roadway Excavation',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '296.42',
                'output_per_hour' => '120.00',
            ],

            [
                'subcategory_id' => '2',
                'item_number' => '902(1)a',
                'description' => 'Reinforcing Steel (Deformed) Grade 40',
                'unit_id' => '17',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '169.27',
                'output_per_hour' => '129.094',
            ],

            [
                'subcategory_id' => '2',
                'item_number' => 'B.20',
                'description' => 'Temporary Enclosure',
                'unit_id' => '12',
                'category_dupa_id' => '3',
                'direct_unit_cost' => '969.18',
                'output_per_hour' => '1',
            ],
            [
                'subcategory_id' => '2',
                'item_number' => 'B.24',
                'description' => 'Scaffolding (Rental)',
                'unit_id' => '4',
                'category_dupa_id' => '3',
                'direct_unit_cost' => '357.23',
                'output_per_hour' => '6.12',
            ],

            [
                'subcategory_id' => '2',
                'item_number' => 'B.4(1)',
                'description' => 'Layout and Staking',
                'unit_id' => '4',
                'category_dupa_id' => '3',
                'direct_unit_cost' => '102.49',
                'output_per_hour' => '100',
            ],

            [
                'subcategory_id' => '2',
                'item_number' => '801(6)',
                'description' => 'Removal of Structures and Obstruction',
                'unit_id' => '5',
                'category_dupa_id' => '1',
                'direct_unit_cost' => '6691.69',
                'output_per_hour' => '2',
            ],


            [
                'subcategory_id' => '3',
                'item_number' => '1000(1)',
                'description' => 'Soil Poisoning',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '411.20',
                'output_per_hour' => '5.4',
            ],
            [
                'subcategory_id' => '2',
                'item_number' => '803(1)a',
                'description' => 'Structure Excavation (Common Soil)',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '264.24',
                'output_per_hour' => '20',
            ],
            [
                'subcategory_id' => '3',
                'item_number' => '804(4)',
                'description' => 'Gravel Fill',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '1802.60',
                'output_per_hour' => '1.2',
            ],
            [
                'subcategory_id' => '4',
                'item_number' => '1601(1)',
                'description' => 'Fill and Backfill',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '546.11',
                'output_per_hour' => '10',
            ],

            [
                'subcategory_id' => '4',
                'item_number' => '104(2)a',
                'description' => 'Embankment from Borrow',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '228.34',
                'output_per_hour' => '50',
            ],
            [
                'subcategory_id' => '4',
                'item_number' => '1500(1)',
                'description' => 'Sand Bedding',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '1175.60',
                'output_per_hour' => '1.2',
            ],
            [
                'subcategory_id' => '5',
                'item_number' => '900(1)c1',
                'description' => 'Structural Concrete, Class A, 28 days (3000 psi)',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '6366.78',
                'output_per_hour' => '3.72',
            ],
            [
                'subcategory_id' => '5',
                'item_number' => '900(1)c2',
                'description' => 'Structural Concrete, Class A, 28 days (4000 psi)',
                'unit_id' => '5',
                'category_dupa_id' => '2',
                'direct_unit_cost' => '6774.78',
                'output_per_hour' => '3.72',
            ],


        ];

        foreach ($dupa as $dupas) {
            Dupa::create([
                'subcategory_id' => $dupas['subcategory_id'],
                'item_number' => $dupas['item_number'],
                'description' => $dupas['description'],
                'unit_id' => $dupas['unit_id'],
                'category_dupa_id' => $dupas['category_dupa_id'],
                'direct_unit_cost' => $dupas['direct_unit_cost'],
                'output_per_hour' => $dupas['output_per_hour'],
            ]);
        }
    }
}
