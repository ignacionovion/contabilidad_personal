<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GastoRecurrente extends Model
{
    protected $table = 'gastos_recurrentes';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'icono',
    ];

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'gasto_recurrente_id');
    }
}
