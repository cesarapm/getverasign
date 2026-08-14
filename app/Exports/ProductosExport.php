<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // Datos
    public function collection()
    {
        return collect($this->data);
    }

    // Encabezados
    public function headings(): array
    {
        // Toma las llaves del primer registro como encabezados
        return array_keys($this->data[0] ?? []);
    }
}
