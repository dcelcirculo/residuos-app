<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedTinyInteger('recolecciones_por_semana')
                ->default(1)
                ->comment('Veces por semana que se realizará la recolección (1 o 2)')
                ->after('frecuencia');

            $table->unsignedSmallInteger('turno_ruta')
                ->nullable()
                ->comment('Turno del usuario dentro de la ruta de recolección')
                ->after('recolecciones_por_semana');

            $table->timestamp('recordatorio_prev_enviado_at')
                ->nullable()
                ->comment('Marca cuando se envió el recordatorio del día anterior')
                ->after('fecha_programada');

            $table->timestamp('recordatorio_dia_enviado_at')
                ->nullable()
                ->comment('Marca cuando se envió el recordatorio del mismo día')
                ->after('recordatorio_prev_enviado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn([
                'recordatorio_dia_enviado_at',
                'recordatorio_prev_enviado_at',
                'turno_ruta',
                'recolecciones_por_semana',
            ]);
        });
    }
};
