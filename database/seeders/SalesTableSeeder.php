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
        $sales = [
            [
                'status' => 1,
                'total' => 500.75,
                'customerId' => 3, // ID de un cliente existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 1,
                'total' => 1020.50,
                'customerId' => 3, // ID de un cliente existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 1,
             
                'total' => 250.99,
                'customerId' => 3, // ID de un cliente existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('sales')->insert($sales);
        
        
    }
}
