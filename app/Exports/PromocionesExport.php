<?php

namespace App\Exports;

use App\Models\Promocion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PromocionesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($promocion) {
            return [
                'ID'           => $promocion->id,
                'Producto'     => $promocion->producto?->nombre_comercial ?? '—',
                'Título'       => $promocion->titulo,
                'Descuento'    => $promocion->descuento,
                'Inicio'       => $promocion->inicio,
                'Fin'          => $promocion->fin,
                'Creado en'    => $promocion->created_at,
                'Actualizado'  => $promocion->updated_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Producto',
            'Título',
            'Descuento',
            'Inicio',
            'Fin',
            'Creado en',
            'Actualizado',
        ];
    }
}
