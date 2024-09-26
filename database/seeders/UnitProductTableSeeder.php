<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitProducts = [
            [
                'unitPrice' => 10.00,
                'measurementUnit' => 'Kilo',
                'status' => 1,
                'productId' => 1,
            ],
            [
                'unitPrice' => 2.50,
                'measurementUnit' => 'Cuartilla',
                'status' => 1,
                'productId' => 1,
            ],
            [
                'unitPrice' => 5.00,
                'measurementUnit' => 'Libra',
                'status' => 1,
                'productId' => 1,
            ],
            [
                'unitPrice' => 20.00,
                'measurementUnit' => 'Arroba',
                'status' => 1,
                'productId' => 1,
            ],
            [
                'unitPrice' => 100.00,
                'measurementUnit' => 'Quintal',
                'status' => 1,
                'productId' => 1,
            ],
        ];

        DB::table('unit_products')->insert($unitProducts);  
    }
}
