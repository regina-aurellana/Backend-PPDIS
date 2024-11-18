<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Formula;

class FormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formula = [
            [
                'result' => 'AREA',
                'formula' => 'LENGTH*WIDTH',

            ],
            [
                'result' => 'AREA',
                'formula' => '(2*LENGTH)*WIDTH+10',

            ],
            [
                'result' => 'VOLUME',
                'formula' => 'LENGTH*WIDTH*DEPTH',

            ],
            [
                'result' => 'LENGTH',
                'formula' => '((LENGTH+(2*OFFSET))*2)+((WIDTH+(2*OFFSET))*2)',

            ],
            [
                'result' => 'AREA',
                'formula' => 'PERIMETER*HEIGHT',

            ],

            [
                'result' => 'VOLUME',
                'formula' => 'LENGTH*WIDTH*HEIGHT*QUANTITY',

            ],
            [
                'result' => 'VOLUME',
                'formula' => 'LENGTH*WIDTH*DEPTH*QUANTITY',

            ],
            [
                'result' => 'VOLUME',
                'formula' => 'LENGTH*WIDTH*THICKNESS*QUANTITY',

            ],
            [
                'result' => 'TOTAL',
                'formula' => 'TOTAL',

            ],

        ];

        foreach($formula as $formulas){
            Formula::create([
                'result' => $formulas['result'],
                'formula' => $formulas['formula'],
            ]);
        }
    }
}
