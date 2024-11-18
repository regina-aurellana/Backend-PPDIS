<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DupaPerProject;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            ProjectNatureSeeder::class,
            ProjectNatureTypeSeeder::class,
            B3ProjectSeeder::class,
            MaterialSeeder::class,
            LaborSeeder::class,
            EquipmentSeeder::class,
            SowCategorySeeder::class,
            SowSubCategorySeeder::class,
            UnitOfMeasurementSeeder::class,
            CategoryDupaSeeder::class,
            DupaSeeder::class,
            DupaContentSeeder::class,
            DupaLaborSeeder::class,
            DupaEquipmentSeeder::class,
            DupaMaterialSeeder::class,
            DupaPerProjectGroupSeeder::class,
            DupaPerProjectSeeder::class,
            DupaContentPerProjectSeeder::class,
            DupaLaborPerProjectSeeder::class,
            DupaEquipmentPerProjectSeeder::class,
            DupaMaterialPerProjectSeeder::class,
            SubCatReferenceSeeder::class,
            TakeOffSeeder::class,
            FormulaSeeder::class,
            // B3PowSeeder::class,
            TableComponentSeeder::class,
            TableComponentFormulaSeeder::class,
            // TakeOffTableSeeder::class,
            // TakeOffTableFieldSeeder::class,
            RoleSeeder::class,
            TeamSeeder::class,
            UserSeeder::class,
            CommunicationCategorySeeder::class,
            DistrictSeeder::class,
            BarangaySeeder::class,

            // TakeOffTableFieldInputSeeder::class,
        ]);
    }
}
