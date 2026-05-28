<?php

namespace App\Models;
use App\Models\Client;
use App\Enums\Address\AddressTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    // Usando o padrão do Laravel para fillable (ou mantenha seu atributo #[Fillable] se estiver usando um pacote específico para isso)
        protected  $fillable = [
            'client_id', // Necessário para o vínculo com o usuário
            'tipo_endereco',
            'logradouro',
            'numero',
            'cep',
            'bairro',
            'cidade',
            'complemento',
            'uf'
        ];

        protected function casts(): array
        {
            return [
                'tipo_endereco' => AddressTypes::class,
            ];
        }

        public function client(): BelongsTo
        {
            return $this->belongsTo(Client::class);
        }

        public function rental(): HasMany
        {
            return $this->hasMany(Rental::class);
        }

}
