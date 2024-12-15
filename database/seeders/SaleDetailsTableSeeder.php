<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sale_details = [
            [
                'quantity' => 2,
                'unitPrice' => 250.38,
                'totalProduct' => 500.76,
                'salesId' => 1,
                'producerId' => 1, // ID de un productor existente
                'productsId' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 4,
                'unitPrice' => 255.12,
                'totalProduct' => 1020.48,
                'salesId' => 2,
                'producerId' => 1, // ID de un productor existente
                'productsId' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 4,
                'unitPrice' => 255.12,
                'totalProduct' => 1020.48,
                'salesId' => 3,
                'producerId' => 1, // ID de un productor existente
                'productsId' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 1,
                'unitPrice' => 250.99,
                'totalProduct' => 250.99,
                'salesId' => 3,
                'producerId' => 1, // ID de un productor existente
                'productsId' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('sale_details')->insert($sale_details);
        
    }
}
