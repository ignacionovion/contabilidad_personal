<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = ['monto', 'descripcion', 'fecha', 'user_id', 'categoria_id', 'es_sueldo', 'activo'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
