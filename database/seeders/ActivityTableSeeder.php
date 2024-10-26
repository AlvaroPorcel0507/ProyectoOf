<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Preparación del suelo',
                'description' => 'Labranza y preparación del terreno para la siembra.',
                'scheduledDate' => now()->addDays(5), // Fecha programada para dentro de 5 días
                'duration' => now()->addDays(5)->addHours(2), // Duración de 2 horas
                'priority' => 2, // Nivel de prioridad
                'status' => 1,
                'idUser' => 1, // ID del usuario responsable
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siembra de maíz',
                'description' => 'Siembra de maíz en la parcela 3.',
                'scheduledDate' => now()->addDays(10), // Fecha programada para dentro de 10 días
                'duration' => now()->addDays(10)->addHours(4), // Duración de 4 horas
                'priority' => 1, // Nivel de prioridad
                'status' => 1,
                'idUser' => 2, // ID del usuario responsable
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aplicación de fertilizantes',
                'description' => 'Aplicación de fertilizantes en cultivos de papa.',
                'scheduledDate' => now()->addDays(7), // Fecha programada para dentro de 7 días
                'duration' => now()->addDays(7)->addHours(3), // Duración de 3 horas
                'priority' => 3, // Nivel de prioridad
                'status' => 1,
                'idUser' => 3, // ID del usuario responsable
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cosecha de trigo',
                'description' => 'Recolección de la cosecha de trigo en la parcela 1.',
                'scheduledDate' => now()->addDays(15), // Fecha programada para dentro de 15 días
                'duration' => now()->addDays(15)->addHours(5), // Duración de 5 horas
                'priority' => 1, // Nivel de prioridad
                'status' => 1,
                'idUser' => 1, // ID del usuario responsable
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('activities')->insert($activities);
    }
}
