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
        DB::table('sale_details')->insert([
            [
                'quantity' => 2,
                'unitPrice' => 250.38,
                'totalProduct' => 500.76,  // Cantidad * Precio unitario
                'salesId' => 1,  // ID de la venta
                'productsId' => 1,  // ID del producto
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'quantity' => 4,
                'unitPrice' => 255.12,
                'totalProduct' => 1020.48,
                'salesId' => 1,
                'productsId' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'quantity' => 1,
                'unitPrice' => 250.99,
                'totalProduct' => 250.99,
                'salesId' => 3,
                'productsId' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
