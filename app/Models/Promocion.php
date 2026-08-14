<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Promocion extends Model
{
    use HasFactory;

    // 🔧 Tabla explícita
    protected $table = 'promociones';

    protected $fillable = [
        'producto_id',
        'titulo',
        'descuento',
        'inicio',
        'fin',
    ];

    // 🔁 Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
