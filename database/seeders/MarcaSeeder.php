<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Serviram Diseño', 'descripcion' => 'Marca propia de diseño artesanal', 'orden' => 1],
            ['nombre' => 'Artesanía Maya', 'descripcion' => 'Piezas de tradición maya', 'orden' => 2],
            ['nombre' => 'Oro Puro', 'descripcion' => 'Joyería de oro de alta calidad', 'orden' => 3],
            ['nombre' => 'Plata Nativa', 'descripcion' => 'Plata y accesorios naturales', 'orden' => 4],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
    }
}
