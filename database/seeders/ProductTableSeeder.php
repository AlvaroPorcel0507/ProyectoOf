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
                'description' => 'La papa es uno de los alimentos básicos más populares y consumidos en todo el mundo',
                'unitPrice' => 25.00, // Añadido el campo price
                'stock' => 100.00,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 4, // Asegúrate de que este ID de usuario exista
                'categoryId' => 3, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenteja',
                'description' => 'Las lentejas son un tipo de legumbre altamente nutritiva y versátil, apreciada por su rico contenido en proteínas, fibra, vitaminas y minerales esenciales',
                'unitPrice' => 50.75, // Añadido el campo price
                'stock' => 200.50,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 4, // Asegúrate de que este ID de usuario exista
                'categoryId' => 1, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Acelga',
                'description' => 'La acelga es una hoja verde fresca y sabrosa, reconocida por su alto contenido en nutrientes esenciales',
                'unitPrice' => 0, // Añadido el campo price
                'stock' => 0,
                'measurementUnit'=>'Caja',
                'status' => 1, // Producto inactivo
                'userId' => 2, // Asegúrate de que este ID de usuario exista
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 4',
                'description' => 'Description of Product 4',
                'unitPrice' => 100.50, // Añadido el campo price
                'stock' => 150.75,
                'measurementUnit'=>'Caja',
                'status' => 0,
                'userId' => 3, // Asegúrate de que este ID de usuario exista
                'categoryId' => 3, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 5',
                'description' => 'Description of Product 5',
                'unitPrice' => 20.00, // Añadido el campo price
                'stock' => 50.00,
                'measurementUnit'=>'Caja',
                'status' => 0,
                'userId' => 2, // Sin usuario asignado
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertar datos
        DB::table('products')->insert($products);
    }
}
