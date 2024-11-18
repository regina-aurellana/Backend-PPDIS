<?php

namespace Database\Seeders;

use App\Models\CommunicationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunicationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'description' => 'Appointment',
                'created_at' => now()
            ],
            [
                'description' => 'Meeting',
                'created_at' => now()
            ],
            [
                'description' => 'Project',
                'created_at' => now()
            ],
            [
                'description' => 'Report',
                'created_at' => now()
            ],
        ];

        CommunicationCategory::insert($categories);
    }
}
