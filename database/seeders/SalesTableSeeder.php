<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales')->insert([
            [
                'status' => 1,
                'userId' => 3,  // ID del usuario que realizó la venta
                'total' => 500.75,
                'customerId' => 3,  // ID del cliente
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'status' => 1,
                'userId' => 3,
                'total' => 1020.50,
                'customerId' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'status' => 0,
                'userId' => 3,
                'total' => 250.99,
                'customerId' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
