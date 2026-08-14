<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_comercial',
        'slug',
        'modelo',
        'item',
        'categoria_id',
        'marca_id',
        'destacado',
        'descripcion',
        'precio',
        'galeria_imagenes',
        'imagen_slug',
        'medidas',
        'peso',
        'industria',
        'tipo',
        'ficha',
        'manual',
        'activo',
        'orden',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'precio' => 'decimal:2',
        'orden' => 'decimal:2',
        'galeria_imagenes' => 'array',
        'industria'=> 'array',

    ];

    // Relaciones

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
    public function promocion()
    {
    return $this->hasOne(Promocion::class);
    }
    protected static function booted()
    {
        static::creating(function ($producto) {
            $producto->slug = Str::slug($producto->nombre_comercial);
        });
        static::creating(function ($producto) {
            if (is_null($producto->orden)) {
                $maxOrden = static::max('orden');
                $producto->orden = $maxOrden !== null ? $maxOrden + 1 : 1;
            }
        });
        static::deleting(function ($producto) {
            // Eliminar imagen_slug
            if (!empty($producto->imagen_slug)) {
                if (Storage::disk('public')->exists($producto->imagen_slug)) {
                    Storage::disk('public')->delete($producto->imagen_slug);
                }
            }

            // Eliminar ficha
            if (!empty($producto->ficha)) {
                if (Storage::disk('public')->exists($producto->ficha)) {
                    Storage::disk('public')->delete($producto->ficha);
                }
            }

            // Eliminar manual
            if (!empty($producto->manual)) {
                if (Storage::disk('public')->exists($producto->manual)) {
                    Storage::disk('public')->delete($producto->manual);
                }
            }

            // Eliminar galería de imágenes
            if (is_array($producto->galeria_imagenes)) {
                foreach ($producto->galeria_imagenes as $imagenPath) {
                    if (Storage::disk('public')->exists($imagenPath)) {
                        Storage::disk('public')->delete($imagenPath);
                    }
                }
            }
        });
    }
}
