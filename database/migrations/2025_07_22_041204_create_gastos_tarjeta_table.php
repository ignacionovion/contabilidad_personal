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
        Schema::create('gastos_tarjeta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_credito_id')->constrained('tarjetas_credito')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('descripcion');
            $table->integer('monto_cuota');
            $table->integer('numero_cuota');
            $table->integer('total_cuotas');
            $table->date('fecha');
            $table->unsignedBigInteger('gasto_padre_id')->nullable(); // Para agrupar cuotas
            $table->foreign('gasto_padre_id')->references('id')->on('gastos_tarjeta')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos_tarjeta');
    }
};
