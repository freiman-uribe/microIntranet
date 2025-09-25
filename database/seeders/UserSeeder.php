<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'edad' => 30,
            'email' => 'admin@microintranet.com',
            'password' => Hash::make('admin123'),
            'admin' => true,
            'token' => \Str::random(60),
        ]);

        // Usuarios normales
        User::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'edad' => 25,
            'email' => 'juan@microintranet.com',
            'password' => Hash::make('123456'),
            'admin' => false,
            'token' => \Str::random(60),
        ]);

        User::create([
            'nombre' => 'María',
            'apellido' => 'González',
            'edad' => 28,
            'email' => 'maria@microintranet.com',
            'password' => Hash::make('123456'),
            'admin' => false,
            'token' => \Str::random(60),
        ]);

        User::create([
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'edad' => 17, // Usuario menor de edad para probar el Observer
            'email' => 'carlos@microintranet.com',
            'password' => Hash::make('123456'),
            'admin' => false,
            'token' => \Str::random(60),
        ]);
    }
}
