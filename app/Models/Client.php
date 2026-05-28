<?php

namespace App\Models;

use App\Enums\Client\ClientTypes;
use App\Models\Address;
use App\Enums\Address\AddressTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Client extends Model
{
    protected  $fillable = [
    'nome',
    'natureza_juridica',
    'cpf_cnpj',
    ];

    protected function casts(): array
    {
        return [
            'natureza_juridica' => ClientTypes::class,
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Rental::class);
    }
}
