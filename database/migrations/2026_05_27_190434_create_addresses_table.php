<?php

use App\Enums\Address\AddressTypes;
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
             Schema::create('addresses', function (Blueprint $table) {
                 $table->id();

                 // Relacionamento forte com a tabela clients
                 $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                 $table->enum('tipo_endereco', array_column(AddressTypes::cases(),'value'))->default(AddressTypes::Locacao->value);
                 $table->string('cep', 9);
                 $table->string('logradouro', 150);
                 $table->string('numero', 20)->nullable();
                 $table->string('complemento', 100)->nullable();
                 $table->string('bairro', 100);
                 $table->string('cidade', 100);
                 $table->char('uf', 2);

                 $table->timestamps();
             });
         }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
