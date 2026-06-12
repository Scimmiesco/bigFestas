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
        Schema::table('rentals', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            
            // Torna as colunas de string opcionais
            $table->string('cliente_nome')->nullable()->change();
            $table->string('endereco_entrega')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['address_id']);
            $table->dropColumn(['client_id', 'address_id']);
            
            // Reverte as colunas de string para obrigatórias
            $table->string('cliente_nome')->nullable(false)->change();
            $table->string('endereco_entrega')->nullable(false)->change();
        });
    }
};
