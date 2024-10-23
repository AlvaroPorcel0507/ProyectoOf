<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Legumbres',
                'status' => 1,
                'userId' => 1, 
            ],
            [
                'name' => 'Hortalizas',
                'status' => 1,
                'userId' => 1, 
            ],
            [
                'name' => 'Tuberculos',
                'status' => 1,
                'userId' => 1, 
            ],
            [
                'name' => 'Books',
                'status' => 0, 
                'userId' => 3, 
            ],
            [
                'name' => 'Toys',
                'status' => 0,
                'userId' => null, // Sin usuario asignado
            ],
        ];

        // Insertar datos
        DB::table('categories')->insert($categories);
    }
}
