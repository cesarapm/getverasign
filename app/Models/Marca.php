<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', // ajusta según tus columnas reale
        'descripcion',
        'orden'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    protected static function booted()
    {

        static::creating(function ($producto) {
            if (is_null($producto->orden)) {
                $maxOrden = static::max('orden');
                $producto->orden = $maxOrden !== null ? $maxOrden + 1 : 1;
            }
        });
    }
}
