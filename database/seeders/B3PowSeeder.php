<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProgramOfWork;

class B3PowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pow = [
            [
                'b3_project_id' => "1",
            ],
            [
                'b3_project_id' => "2",
            ],
        ];

        ProgramOfWork::insert($pow);
    }
}
