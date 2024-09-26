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
                'name' => 'Electronics',
                'status' => 1,
                'userId' => 1, // Asegúrate de que este ID de usuario exista
            ],
            [
                'name' => 'Clothing',
                'status' => 1,
                'userId' => 2, // Asegúrate de que este ID de usuario exista
            ],
            [
                'name' => 'Home Appliances',
                'status' => 1,
                'userId' => null, // Sin usuario asignado
            ],
            [
                'name' => 'Books',
                'status' => 0, // Categoría inactiva
                'userId' => 3, // Asegúrate de que este ID de usuario exista
            ],
            [
                'name' => 'Toys',
                'status' => 1,
                'userId' => null, // Sin usuario asignado
            ],
        ];

        // Insertar datos
        DB::table('categories')->insert($categories);
    }
}
