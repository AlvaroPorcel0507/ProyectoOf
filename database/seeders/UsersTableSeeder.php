<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the users table.
     *
     * @return void
     */
    public function run()
    {
        // Insert multiple users
        DB::table('users')->insert([
            [
                'name' => 'Alvaro Ivan',
                'lastName' => 'Porcel',
                'secondLastName' => 'Moreno',
                'role' => 'Administrador',
                'location' => 'Urbanizacion Manantial',
                'status' => 1,
                'email' => 'porcel.moreno.alvaro@gmail.com',
                'companyName' => 'Porcel',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Contraseña encriptada
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan',
                'lastName' => 'Perez',
                'secondLastName' => 'Calisaya',
                'role' => 'Productor',
                'location' => 'Calle los lirios',
                'status' => 1,
                'email' => 'Jperez@gmail.com',
                'companyName' => 'Perez',
                'email_verified_at' => now(),
                'password' => Hash::make('hola1234'), // Contraseña encriptada
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos',
                'lastName' => 'Gonzalez',
                'secondLastName' => 'Perez',
                'role' => 'Cliente',
                'location' => 'Avenida Ayacucho',
                'status' => 1,
                'email' => 'carlosg@gmail.com',
                'companyName' => 'Gonzalez',
                'email_verified_at' => now(),
                'password' => Hash::make('carlos1234'),
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
