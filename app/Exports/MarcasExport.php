<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MarcasExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Collection $marcas;

    public function __construct(Collection $marcas)
    {
        $this->marcas = $marcas;
    }

    public function collection()
    {
        return $this->marcas->map(function ($marca) {
            return [
                'ID'           => $marca->id,
                'Nombre'       => $marca->nombre,
                'Descripción'  => $marca->descripcion,
                'Orden'        => $marca->orden,
                'Productos'    => $marca->productos()->count(),
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
