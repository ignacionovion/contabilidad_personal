<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = ['monto', 'descripcion', 'categoria_id', 'user_id', 'fecha', 'gasto_recurrente_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function gastoRecurrente(): BelongsTo
    {
        return $this->belongsTo(GastoRecurrente::class, 'gasto_recurrente_id');
    }
}
