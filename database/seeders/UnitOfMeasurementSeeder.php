<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitOfMeasurement;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $measurement = [
            [
                'name' => 'HEIGHT',
                'abbreviation' => 'h',
            ],
            [
                'name' => 'LENGTH',
                'abbreviation' => 'l',
            ],
            [
                'name' => 'VOLUME',
                'abbreviation' => 'v',
            ],
            [
                'name' => 'SQUARE METER',
                'abbreviation' => 'sq.m.',
            ],
            [
                'name' => 'CUBIC METER',
                'abbreviation' => 'cu.m.',
            ],
            [
                'name' => 'YARD',
                'abbreviation' => 'yd.',
            ],
            [
                'name' => 'HECTARE',
                'abbreviation' => 'ha.',
            ],
            [
                'name' => 'METER',
                'abbreviation' => 'm',
            ],
            [
                'name' => 'WIDTH',
                'abbreviation' => 'w',
            ],
            [
                'name' => 'AREA',
                'abbreviation' => 'A',
            ],
            [
                'name' => 'DEPTH',
                'abbreviation' => 'd',
            ],
            [
                'name' => 'THICKNESS',
                'abbreviation' => 'thickness',
            ],
            [
                'name' => 'LINEAR METER',
                'abbreviation' => 'l.m',
            ],
            [
                'name' => 'PERIMETER',
                'abbreviation' => 'P',
            ],
            [
                'name' => 'QUANTITY',
                'abbreviation' => 'qty.',
            ],
            [
                'name' => 'DEDUCTION',
                'abbreviation' => 'deduct',
            ],
            [
                'name' => 'ADDITION',
                'abbreviation' => 'add',
            ],
            [
                'name' => 'EACH',
                'abbreviation' => 'each',
            ],
            [
                'name' => 'TOTAL',
                'abbreviation' => 'total',
            ],
            [
                'name' => 'WEIGTH',
                'abbreviation' => 'weight',
            ],
            [
                'name' => 'OFFSET',
                'abbreviation' => 'offset',
            ],
            [
                'name' => 'W / 6M',
                'abbreviation' => 'w / 6m',
            ],
            [
                'name' => 'REBAR DIAMETER',
                'abbreviation' => 'reabr diameter',
            ],
            [
                'name' => 'BASE',
                'abbreviation' => 'b',
            ],
            [
                'name' => 'DIAMETER OF MAIN BAR',
                'abbreviation' => 'diameter of main bar',
            ],
            [
                'name' => 'DIAMETER OF STIRRUP',
                'abbreviation' => 'Diameter of Stirrup',
            ],
            [
                'name' => 'TOP LEFT',
                'abbreviation' => 'top left',
            ],
            [
                'name' => 'TOP MID',
                'abbreviation' => 'top mid',
            ],
            [
                'name' => 'TOP RIGHT',
                'abbreviation' => 'top right',
            ],
            [
                'name' => 'BOTTOM LEFT',
                'abbreviation' => 'bottom left',
            ],
            [
                'name' => 'BOTTOM MID',
                'abbreviation' => 'bottom mid',
            ],
            [
                'name' => 'BOTTOM RIGHT',
                'abbreviation' => 'bottom right',
            ],
            [
                'name' => 'REST',
                'abbreviation' => 'rest',
            ],
            [
                'name' => 'WEIGHT OF REBAR PER 6M',
                'abbreviation' => 'weight of rebar per 6m',
            ],
            [
                'name' => 'WEIGHT OF STIRRUPS PER 6M',
                'abbreviation' => 'weight of stirrup per 6m',
            ],
            [
                'name' => 'DIAMETER OF WEB BAR',
                'abbreviation' => 'Diameter Web Bar',
            ],
            [
                'name' => 'NO OF WEB BAR',
                'abbreviation' => 'No of Web Bar',
            ],
            [
                'name' => 'WEIGHT OF WEB BARS PER 6M',
                'abbreviation' => 'weight of web bars per 6m',
            ],




        ];

        foreach($measurement as $measurements){
            UnitOfMeasurement::create([
                'name' => $measurements['name'],
                'abbreviation' => $measurements['abbreviation'],
            ]);
        }
    }
}
