<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Client\ClientTypes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nome',150);
            // Usando enum para garantir que só entre 'PF' ou 'PJ'
            $table->enum('natureza_juridica', array_column(ClientTypes::cases(), 'value'))
                              ->default(ClientTypes::PF->value);
            // Limitamos a 14 caracteres (tamanho máximo do CNPJ sem pontuação) e o tornamos único
            $table->string('cpf_cnpj', 14)->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
