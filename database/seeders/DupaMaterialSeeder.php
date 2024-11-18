<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DupaMaterial;

class DupaMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dupaMaterial = [
            [
                'dupa_content_id' => '1',
                'material_id' => '1',
                'quantity' => '2',
            ],
            [
                'dupa_content_id' => '1',
                'material_id' => '5',
                'quantity' => '2',
            ],
            [
                'dupa_content_id' => '1',
                'material_id' => '7',
                'quantity' => '1',
            ],
            [
                'dupa_content_id' => '1',
                'material_id' => '8',
                'quantity' => '3',
            ],

            [
                'dupa_content_id' => '4',
                'material_id' => '41',
                'quantity' => '2.40',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '42',
                'quantity' => '10.36',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '43',
                'quantity' => '1.50',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '44',
                'quantity' => '0.54',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '45',
                'quantity' => '0.02',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '46',
                'quantity' => '0.05',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '47',
                'quantity' => '1.78',
            ],
            [
                'dupa_content_id' => '4',
                'material_id' => '48',
                'quantity' => '1.86',
            ],
            [
                'dupa_content_id' => '5',
                'material_id' => '48',
                'quantity' => '2',
            ],
            [
                'dupa_content_id' => '8',
                'material_id' => '49',
                'quantity' => '1',
            ],
            [
                'dupa_content_id' => '8',
                'material_id' => '50',
                'quantity' => '2',
            ],
            [
                'dupa_content_id' => '10',
                'material_id' => '52',
                'quantity' => '1.05',
            ],
            [
                'dupa_content_id' => '13',
                'material_id' => '53',
                'quantity' => '1.20',
            ],
            [
                'dupa_content_id' => '14',
                'material_id' => '4',
                'quantity' => '1',
            ],
            [
                'dupa_content_id' => '15',
                'material_id' => '6',
                'quantity' => '1',
            ],


        ];

        foreach($dupaMaterial as $dupaMaterials){
            DupaMaterial::create([
                'dupa_content_id' => $dupaMaterials['dupa_content_id'],
                'material_id' => $dupaMaterials['material_id'],
                'quantity' => $dupaMaterials['quantity'],
            ]);
        }
    }
}
