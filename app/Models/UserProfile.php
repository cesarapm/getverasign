<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'telefono',
        'calle',
        'numero_exterior',
        'numero_interior',
        'colonia',
        'entre_calles',
        'codigo_postal',
        'ciudad',
        'estado',
        'pais',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
