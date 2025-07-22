<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TarjetaCredito;

class GastoTarjeta extends Model
{
    use HasFactory;

    protected $table = 'gastos_tarjeta';

    protected $fillable = [
        'tarjeta_credito_id',
        'user_id',
        'descripcion',
        'monto_cuota',
        'numero_cuota',
        'total_cuotas',
        'fecha',
        'gasto_padre_id',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarjetaCredito()
    {
        return $this->belongsTo(TarjetaCredito::class);
    }
}
