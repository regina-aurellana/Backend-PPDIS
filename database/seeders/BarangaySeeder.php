<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $district_1 = [
            [
                'name' => 'Alicia',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Bagong Pag-asa',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Bahay Toro',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Balingasa',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Bungad',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Damar',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Damayan',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Del Monte',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Katipunan',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Mariblo',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Masambong',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'N.S. Amoranto (Gintong Silahis)',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Nayong Kanluran',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Paang Bundok',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Pag-ibig sa Nayon',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Paltok',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Paraiso',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Phil-Am',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Project 6',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Ramon Magsaysay',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Saint Peter',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Salvacion',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'San Antonio',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'San Isidro Labrador',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'San Jose',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Santa Cruz',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Santa Teresita',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Santo Cristo',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Santo Domingo',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Siena',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Talayan',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Vasra',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Veterans Village',
                'district_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'West Triangle',
                'district_id' => 1,
                'created_at' => now()
            ],
        ];

        $district_2 = [
            [
                'name' => 'Bagong Silangan',
                'district_id' => 2,
                'created_at' => now()
            ],
            [
                'name' => 'Batasan Hills',
                'district_id' => 2,
                'created_at' => now()
            ],
            [
                'name' => 'Commonwealth',
                'district_id' => 2,
                'created_at' => now()
            ],
            [
                'name' => 'Holy Spirit',
                'district_id' => 2,
                'created_at' => now()
            ],
            [
                'name' => 'Payatas',
                'district_id' => 2,
                'created_at' => now()
            ],
        ];

        $district_3 = [
            [
                'name' => 'Amihan',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Bagumbuhay',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Bagumbayan',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Bayanihan',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Blue Ridge A',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Blue Ridge B',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Camp Aguinaldo',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Claro',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Dioquino Zobel',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Duyan-Duyan',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'E. Rodriguez',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'East Kamias',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Escopa I',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Escopa II',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Escopa III',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Escopa IV',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Libis',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Loyola Heights',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Mangga',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Marilag',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Masagana',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Matandang Balara',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Milagrosa',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Pansol',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Quirino 2-A',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Quirino 2-B',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Quirino 2-C',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Quirino 3-A',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Saint Ignatius',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'San Roque',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Silangan',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Socorro',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Tagumpay',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Ugong Norte',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Villa Maria Clara',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'West Kamias',
                'district_id' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'White Plains',
                'district_id' => 3,
                'created_at' => now()
            ],
        ];

        $district_4 = [
            [
                'name' => 'Bagong Lipunan ng Crame',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Botocan',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Central',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Kristong Hari',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Damayang Lagi',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Doña Aurora',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Doña Imelda',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Doña Josefa',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Don Manuel',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'East Triangle',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Horseshoe',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Immaculate Conception',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Kalusugan',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Kamuning',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Kaunlaran',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Krus na Ligas',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Laging Handa',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Malaya',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Mariana',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Old Capitol Site',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Paligsahan',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Pinyahan',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Pinagkaisahan',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'QMC',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Roxas',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Sacred Heart',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'San Isidro Galas',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'San Martin de Porres (Cubao)',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'San Vicente',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Santo Niño',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Santol',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Tatalon',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Teachers Village East',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Teachers Village West',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'U.P. Campus',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'U.P. Village',
                'district_id' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Valencia',
                'district_id' => 4,
                'created_at' => now()
            ],
        ];

        $district_5 = [
            [
                'name' => 'Bagbag',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Capri',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Fairview',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Greater Lagro',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Gulod',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Kaligayahan',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Nagkaisang Nayon',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'North Fairview',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Novaliches Proper',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Pasong Putik Proper',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'San Agustin',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'San Bartolome',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Santa Lucia',
                'district_id' => 5,
                'created_at' => now()
            ],
            [
                'name' => 'Santa Monica',
                'district_id' => 5,
                'created_at' => now()
            ],
        ];

        $district_6 = [
            [
                'name' => 'Apolonio Samson',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Baesa',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Balong-bato',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Culiat',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'New Era',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Pasong Tamo',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Sangandaan',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Sauyo',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Talipapa',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Tandang Sora',
                'district_id' => 6,
                'created_at' => now()
            ],
            [
                'name' => 'Unang Sigaw',
                'district_id' => 6,
                'created_at' => now()
            ],
        ];

        $barangays = array_merge($district_1, $district_2, $district_3, $district_4, $district_5, $district_6);

        foreach (array_chunk($barangays, 500) as $barangay) {
            Barangay::insert($barangay);
        }
    }
}
