<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Papa',
                'description' => 'La papa es uno de los alimentos básicos más populares y consumidos en todo el mundo.',
                'stock' => 100.00,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 3, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenteja',
                'description' => 'Las lentejas son un tipo de legumbre altamente nutritiva y versátil.',
                'stock' => 200.50,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 1, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Acelga',
                'description' => 'La acelga es una hoja verde fresca y sabrosa, reconocida por su alto contenido en nutrientes esenciales.',
                'stock' => 1,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 2, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 4',
                'description' => 'Description of Product 4',
                'stock' => 150.75,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 3, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 5',
                'description' => 'Description of Product 5',
                'stock' => 50.00,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 2, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 6',
                'description' => 'Description of Product 6',
                'stock' => 50.00,
                'status' => 1,
                'userId' => 2,
                'categoryId' => 3, // ID existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('products')->insert($products);
        
    }
}
