<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'sku' => 'LAPTOP001',
                'nombre' => 'Laptop Dell Inspiron 15',
                'precio_unitario' => 899.99
            ],
            [
                'sku' => 'MOUSE001',
                'nombre' => 'Mouse Logitech M100',
                'precio_unitario' => 15.99
            ],
            [
                'sku' => 'KEYBOARD001',
                'nombre' => 'Teclado Logitech K120',
                'precio_unitario' => 25.50
            ],
            [
                'sku' => 'MONITOR001',
                'nombre' => 'Monitor Samsung 24" Full HD',
                'precio_unitario' => 189.99
            ],
            [
                'sku' => 'CABLE001',
                'nombre' => 'Cable HDMI 2m',
                'precio_unitario' => 12.99
            ],
            [
                'sku' => 'WEBCAM001',
                'nombre' => 'Webcam Logitech C270',
                'precio_unitario' => 39.99
            ],
            [
                'sku' => 'SPEAKER001',
                'nombre' => 'Altavoces Creative Pebble',
                'precio_unitario' => 29.99
            ],
            [
                'sku' => 'HDD001',
                'nombre' => 'Disco Duro Externo 1TB',
                'precio_unitario' => 59.99
            ],
            [
                'sku' => 'RAM001',
                'nombre' => 'Memoria RAM DDR4 8GB',
                'precio_unitario' => 79.99
            ],
            [
                'sku' => 'SSD001',
                'nombre' => 'SSD Kingston 256GB',
                'precio_unitario' => 45.99
            ]
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
