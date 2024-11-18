<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TableDupaComponentFormula;

class TableComponentFormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $component_formulas = [
            [
                'table_dupa_component_id' => 1,
                'formula_id' => 2,
            ],
            [
                'table_dupa_component_id' => 2,
                'formula_id' => 1,
            ],
            [
                'table_dupa_component_id' => 3,
                'formula_id' => 1,
            ],
            [
                'table_dupa_component_id' => 4,
                'formula_id' => 4,
            ],
            [
                'table_dupa_component_id' => 5,
                'formula_id' => 5,
            ],
            [
                'table_dupa_component_id' => 6,
                'formula_id' => 1,
            ],
            [
                'table_dupa_component_id' => 7,
                'formula_id' => 6,
            ],
            [
                'table_dupa_component_id' => 8,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' => 9,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>10,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>11,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>12,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>13,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 9,
            ],
            [
                'table_dupa_component_id' =>15,
                'formula_id' => 9,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 8,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 6,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 8,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 8,
            ],
            [
                'table_dupa_component_id' =>14,
                'formula_id' => 7,
            ],
            [
                'table_dupa_component_id' =>15,
                'formula_id' => 6,
            ],
            [
                'table_dupa_component_id' =>15,
                'formula_id' => 6,
            ],
            [
                'table_dupa_component_id' =>15,
                'formula_id' => 6,
            ],
            [
                'table_dupa_component_id' =>15,
                'formula_id' => 8,
            ],




        ];

        TableDupaComponentFormula::insert($component_formulas);

    }
}
