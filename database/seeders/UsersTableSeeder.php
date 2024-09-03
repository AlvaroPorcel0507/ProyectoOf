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
                'name' => 'Alvaro',
                'lastName' => 'Porcel',
                'secondLastName' => '',
                'role' => '1',
                'location' => 'City Center',
                'status' => 1,
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karina',
                'lastName' => 'Esperanza',
                'secondLastName' => 'Ibañes',
                'role' => '2',
                'location' => 'Suburb',
                'status' => 1,
                'email' => 'karina@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis',
                'lastName' => 'Carlos',
                'secondLastName' => 'Berrios',
                'role' => '3',
                'location' => 'Outskirts',
                'status' => 1,
                'email' => 'luis@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
