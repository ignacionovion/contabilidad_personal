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
        // Asigna el user_id 2 (el ID del usuario principal) a todos los gastos recurrentes
        // que actualmente no tienen un dueño asignado. Esto corrige los datos antiguos.
        
        // Usamos DB::table para una operación directa y eficiente.
        // El ID 2 se obtuvo del diagnóstico previo.
        
        \Illuminate\Support\Facades\DB::table('gastos_recurrentes')
            ->whereNull('user_id')
            ->update(['user_id' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Este método se deja intencionalmente vacío. No queremos revertir la asignación
        // de dueños, ya que eso reintroduciría el problema. La asignación es una corrección permanente.
    }
};
