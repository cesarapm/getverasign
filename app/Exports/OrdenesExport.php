<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdenesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Collection $ordenes;

    public function __construct(Collection $ordenes)
    {
        $this->ordenes = $ordenes;
    }

    public function collection()
    {
        return $this->ordenes->map(function ($orden) {
            return [
                'ID'              => $orden->id,
                'Cliente'         => $orden->user?->name ?? '—',
                'Email Cliente'   => $orden->user?->email ?? '—',
                'Total'           => $orden->total,
                'Método de Pago'  => $orden->metodo_pago,
                'Status'          => $orden->status,
                'Activo'          => $orden->activo ? 'Sí' : 'No',
                'Productos'       => $orden->productos->map(fn ($p) => $p->nombre_comercial . ' (x' . $p->pivot->cantidad . ')')->join(', '),
                'Pago Estado'     => $orden->pago?->estado ?? '—',
                'Pago Monto'      => $orden->pago?->monto ?? '—',
                'Fecha de Orden'  => $orden->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Email Cliente',
            'Total',
            'Método de Pago',
            'Status',
            'Activo',
            'Productos',
            'Pago Estado',
            'Pago Monto',
            'Fecha de Orden',
        ];
    }
}
