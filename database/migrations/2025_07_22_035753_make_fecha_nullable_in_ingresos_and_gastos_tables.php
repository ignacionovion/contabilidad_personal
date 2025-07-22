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
        Schema::table('ingresos', function (Blueprint $table) {
            $table->date('fecha')->nullable()->change();
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->date('fecha')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->date('fecha')->nullable(false)->change();
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->date('fecha')->nullable(false)->change();
        });
    }
};
