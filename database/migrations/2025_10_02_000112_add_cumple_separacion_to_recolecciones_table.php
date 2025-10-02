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
        Schema::table('recolecciones', function (Blueprint $table) {
            $table->boolean('cumple_separacion')
                ->default(false)
                ->after('kilos')
                ->comment('Indica si el residuo fue entregado con separación adecuada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recolecciones', function (Blueprint $table) {
            $table->dropColumn('cumple_separacion');
        });
    }
};
