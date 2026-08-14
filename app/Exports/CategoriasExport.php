<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CategoriasExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Collection $categorias;

    public function __construct(Collection $categorias)
    {
        $this->categorias = $categorias;
    }

    public function collection()
    {
        return $this->categorias->map(function ($categoria) {
            return [
                'ID'           => $categoria->id,
                'Nombre'       => $categoria->nombre,
                'Descripción'  => $categoria->descripcion,
                'Orden'        => $categoria->orden,
                'Productos'    => $categoria->productos()->count(),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Orden',
            'Cantidad de Productos',
        ];
    }
}
