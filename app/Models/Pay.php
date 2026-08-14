<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pay extends Model
{
    use HasFactory;

    protected $table = 'pays';

    protected $fillable = [
        'order_id',
        'payment_id',
        'id_pago',
        'descripcion',
        'monto_transaccion',
        'monto_recibido_neto',
        'monto_a_pagar',
        'codigo_autorizacion',
        'estado',
        'fecha_aprobacion',
        'fecha_creacion',
        'fecha_ultima_actualizacion',
        'metodo_pago',
        'numero_tarjeta',
        'ip_direccion',
        'url_notificacion',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
