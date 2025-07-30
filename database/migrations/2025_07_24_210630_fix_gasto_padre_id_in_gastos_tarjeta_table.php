<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corregir los datos existentes: encontrar las compras padre que se apuntan a sí mismas
        // y establecer su gasto_padre_id a NULL para que puedan ser contadas como compras únicas.
        DB::table('gastos_tarjeta')->whereRaw('id = gasto_padre_id')->update(['gasto_padre_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta operación es una corrección de datos y no necesita una operación de rollback.
        // Dejar vacío es intencional.
    }
};
