<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Collares', 'descripcion' => 'Collares de diseño artesanal', 'orden' => 1],
            ['nombre' => 'Pulseras', 'descripcion' => 'Pulseras tejidas y de diseño', 'orden' => 2],
            ['nombre' => 'Aretes', 'descripcion' => 'Aretes delicados y elegantes', 'orden' => 3],
            ['nombre' => 'Anillos', 'descripcion' => 'Anillos en diferentes estilos', 'orden' => 4],
            ['nombre' => 'Accesorios', 'descripcion' => 'Otros accesorios de moda', 'orden' => 5],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
