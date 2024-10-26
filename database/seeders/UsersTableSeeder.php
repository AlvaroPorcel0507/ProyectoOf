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
        $users = [
            [
                'name' => 'Alvaro Ivan',
                'lastName' => 'Porcel',
                'secondLastName' => 'Moreno',
                'role' => 'Administrador',
                'location' => 'Urbanización Manantial',
                'status' => 1,
                'email' => 'porcel.moreno.alvaro@gmail.com',
                'companyName' => 'Porcel',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan',
                'lastName' => 'Pérez',
                'secondLastName' => 'Calisaya',
                'role' => 'Productor',
                'location' => 'Calle los Lirios',
                'status' => 1,
                'email' => 'Jperez@gmail.com',
                'companyName' => 'Pérez',
                'email_verified_at' => now(),
                'password' => Hash::make('hola1234'),
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos',
                'lastName' => 'González',
                'secondLastName' => 'Pérez',
                'role' => 'Cliente',
                'location' => 'Avenida Ayacucho',
                'status' => 1,
                'email' => 'carlosg@gmail.com',
                'companyName' => 'González',
                'email_verified_at' => now(),
                'password' => Hash::make('carlos1234'),
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David',
                'lastName' => 'Rosales',
                'secondLastName' => '',
                'role' => 'Productor',
                'location' => 'Av Aniceto Arce',
                'status' => 1,
                'email' => 'DavidR@gmail.com',
                'companyName' => 'Rosales',
                'email_verified_at' => now(),
                'password' => Hash::make('david1234'),
                'userId' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('users')->insert($users);
        
    }
}
