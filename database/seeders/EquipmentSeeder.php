<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $equipment = [
            [
                'item_code' => '201',
                'name' => 'Mini Wheel Loader (0.75-1.5cum)',
                'hourly_rate' => 1733,
            ],
            [
                'item_code' => '202',
                'name' => 'Wheel Loader (1.5/1.95 cu.yad.)',
                'hourly_rate' => 2450,
            ],
            [
                'item_code' => '203',
                'name' => 'Pneumatic Breaker w/ Air Comp.',
                'hourly_rate' => 2724.8,
            ],
            [
                'item_code' => '204',
                'name' => 'Backhoe (0.80 cu.m.)',
                'hourly_rate' => 1420,
            ],
            [
                'item_code' => '205',
                'name' => 'Backhoe with Breaker (0.80 cu.m.)',
                'hourly_rate' => 2096,
            ],
            [
                'item_code' => '206',
                'name' => 'Dump Truck (12cu.yd)',
                'hourly_rate' => 1420,
            ],
            [
                'item_code' => '207',
                'name' => 'Chain Saw',
                'hourly_rate' => 75.36,
            ],
            [
                'item_code' => '208',
                'name' => 'Plate Compactor (5hp)',
                'hourly_rate' => 123,
            ],
            [   'item_code' => '209',
                'name' => 'Pumpcrete',
                'hourly_rate' => 2076,
            ],
            [
                'item_code' => '210',
                'name' => 'Concrete Vibrator',
                'hourly_rate' => 57.17,
            ],
            [
                'item_code' => '211',
                'name' => 'Vibrator Roller',
                'hourly_rate' => 1846,
            ],
            [
                'item_code' => '212',
                'name' => 'Water Truck/Pump',
                'hourly_rate' => 2450,
            ],
            [
                'item_code' => '213',
                'name' => 'Motorized Road Grader',
                'hourly_rate' => 2173,
            ],
            [
                'item_code' => '214',
                'name' => 'Asphalt Paver',
                'hourly_rate' => 1846,
            ],
            [
                'item_code' => '215',
                'name' => 'Pneumatic Tire Roller',
                'hourly_rate' => 2173,
            ],
            [
                'item_code' => '216',
                'name' => 'Bar Cutter',
                'hourly_rate' => 105.47,
            ],
            [
                'item_code' => '217',
                'name' => 'Bar Bender',
                'hourly_rate' => 168.75,
            ],
            [
                'item_code' => '218',
                'name' => 'Air Compressor ',
                'hourly_rate' => 502.00,
            ],
            [
                'item_code' => '219',
                'name' => 'Jackhammer ',
                'hourly_rate' => 150.00,
            ],
            [
                'item_code' => '220',
                'name' => 'Dump Truck 18 cu.m. capacity ',
                'hourly_rate' => 2500.00,
            ],
            [
                'item_code' => '221',
                'name' => 'Vibratory Roller (125HP)',
                'hourly_rate' => 1846.00,
            ],
            [
                'item_code' => '222',
                'name' => 'Water Truck (16000, 360HP)',
                'hourly_rate' => 2450.00,
            ],



        ];

        foreach($equipment as $equipments){
            Equipment::create([
                'item_code' => $equipments['item_code'],
                'name' => $equipments['name'],
                'hourly_rate' => $equipments['hourly_rate'],
            ]);
        }
    }
}
