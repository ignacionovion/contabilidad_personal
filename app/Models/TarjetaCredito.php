<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\GastoTarjeta;

class TarjetaCredito extends Model
{
    use HasFactory;

    protected $table = 'tarjetas_credito';

    protected $fillable = [
        'user_id',
        'nombre',
        'cupo_total',
        'dia_facturacion',
        'dia_pago',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gastos()
    {
        return $this->hasMany(GastoTarjeta::class);
    }
}
