<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: add_role_to_users_table
 * ----------------------------------------------------------------------------
 * Agrega la columna 'role' a la tabla 'users' para gestionar permisos básicos.
 * Valores esperados: 'user' | 'admin' | 'empresa' (por defecto: 'user').
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nota: si tu tabla ya tiene muchos registros, podrías añadir índice si filtras por 'role'.
            $table->string('role')->default('user'); // 'user' | 'admin' | 'empresa'
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
