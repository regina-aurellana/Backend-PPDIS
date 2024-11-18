<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'name' => '1st District',
                'created_at' => now()
            ],
            [
                'name' => '2nd District',
                'created_at' => now()
            ],
            [
                'name' => '3rd District',
                'created_at' => now()
            ],
            [
                'name' => '4th District',
                'created_at' => now()
            ],
            [
                'name' => '5th District',
                'created_at' => now()
            ],
            [
                'name' => '6th District',
                'created_at' => now()
            ],
        ];

        District::insert($districts);
    }
}
