<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = Categoria::all();
        $marcas = Marca::all();

        $productos = [
            [
                'nombre_comercial' => 'Collar Cosmología Maya',
                'modelo' => 'CM-001',
                'item' => 'COL-CM-001',
                'categoria_id' => $categorias->where('nombre', 'Collares')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Artesanía Maya')->first()->id,
                'destacado' => true,
                'descripcion' => 'Collar diseñado con símbolos de la cosmología maya. Elaborado con materiales nobles y técnicas ancestrales.',
                'precio' => 450.00,
                'medidas' => '45 cm',
                'peso' => '25 g',
                'industria' => ['joyería', 'artesanía'],
                'tipo' => 'collar',
                'activo' => true,
                'orden' => 1,
            ],
            [
                'nombre_comercial' => 'Pulsera Tejida Contemporánea',
                'modelo' => 'PT-002',
                'item' => 'PULS-PT-002',
                'categoria_id' => $categorias->where('nombre', 'Pulseras')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Serviram Diseño')->first()->id,
                'destacado' => true,
                'descripcion' => 'Pulsera tejida a mano con hilos naturales. Diseño moderno que combina tradición y contemporaneidad.',
                'precio' => 280.00,
                'medidas' => '19 cm',
                'peso' => '12 g',
                'industria' => ['joyería', 'textil'],
                'tipo' => 'pulsera',
                'activo' => true,
                'orden' => 2,
            ],
            [
                'nombre_comercial' => 'Aretes Glifo Maya',
                'modelo' => 'AG-003',
                'item' => 'ARE-AG-003',
                'categoria_id' => $categorias->where('nombre', 'Aretes')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Artesanía Maya')->first()->id,
                'destacado' => false,
                'descripcion' => 'Aretes con glifos mayas grabados. Material de plata pura y acabado pulido.',
                'precio' => 320.00,
                'medidas' => '2 cm',
                'peso' => '8 g',
                'industria' => ['joyería'],
                'tipo' => 'arete',
                'activo' => true,
                'orden' => 3,
            ],
            [
                'nombre_comercial' => 'Anillo Oro Puro',
                'modelo' => 'AP-004',
                'item' => 'ANI-AP-004',
                'categoria_id' => $categorias->where('nombre', 'Anillos')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Oro Puro')->first()->id,
                'destacado' => true,
                'descripcion' => 'Anillo en oro de 18 quilates con acabado satinado. Diseño minimalista y elegante.',
                'precio' => 1200.00,
                'medidas' => '18 mm',
                'peso' => '5 g',
                'industria' => ['joyería', 'lujo'],
                'tipo' => 'anillo',
                'activo' => true,
                'orden' => 4,
            ],
            [
                'nombre_comercial' => 'Dije Plata Nativa',
                'modelo' => 'DP-005',
                'item' => 'DIJ-DP-005',
                'categoria_id' => $categorias->where('nombre', 'Accesorios')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Plata Nativa')->first()->id,
                'destacado' => false,
                'descripcion' => 'Dije hecho con plata nativa de alta pureza. Ideal para personalizar tus collares.',
                'precio' => 150.00,
                'medidas' => '1.5 cm',
                'peso' => '4 g',
                'industria' => ['joyería'],
                'tipo' => 'dije',
                'activo' => true,
                'orden' => 5,
            ],
            [
                'nombre_comercial' => 'Collar Mandala Energético',
                'modelo' => 'CME-006',
                'item' => 'COL-CME-006',
                'categoria_id' => $categorias->where('nombre', 'Collares')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Serviram Diseño')->first()->id,
                'destacado' => false,
                'descripcion' => 'Collar con mandala grabado en acero quirúrgico. Símbolo de armonía y energía.',
                'precio' => 380.00,
                'medidas' => '50 cm',
                'peso' => '20 g',
                'industria' => ['joyería', 'accesorios'],
                'tipo' => 'collar',
                'activo' => true,
                'orden' => 6,
            ],
            [
                'nombre_comercial' => 'Pulsera Chakra Balanceada',
                'modelo' => 'PCB-007',
                'item' => 'PULS-PCB-007',
                'categoria_id' => $categorias->where('nombre', 'Pulseras')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Artesanía Maya')->first()->id,
                'destacado' => false,
                'descripcion' => 'Pulsera con 7 piedras naturales que representan los chakras. Bienestar y equilibrio.',
                'precio' => 420.00,
                'medidas' => '20 cm',
                'peso' => '30 g',
                'industria' => ['joyería', 'cristales'],
                'tipo' => 'pulsera',
                'activo' => true,
                'orden' => 7,
            ],
            [
                'nombre_comercial' => 'Aretes Cristal Transparente',
                'modelo' => 'ACT-008',
                'item' => 'ARE-ACT-008',
                'categoria_id' => $categorias->where('nombre', 'Aretes')->first()->id,
                'marca_id' => $marcas->where('nombre', 'Serviram Diseño')->first()->id,
                'destacado' => false,
                'descripcion' => 'Aretes con cristal transparente y montura de plata. Elegancia minimalista.',
                'precio' => 250.00,
                'medidas' => '1.2 cm',
                'peso' => '6 g',
                'industria' => ['joyería'],
                'tipo' => 'arete',
                'activo' => true,
                'orden' => 8,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
