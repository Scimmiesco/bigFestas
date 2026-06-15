<?php

namespace App\Models;

use App\Enums\Rental\RentalStatus;
use App\Models\Item;
use App\Models\Team;
use App\Models\Payment;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    protected $fillable = [
        'team_id', 'client_id', 'address_id', 'cliente_nome', 'endereco_entrega',
        'data_entrega', 'data_recolha', 'status', 'valor', 'observacoes'
    ];

    protected $casts = [
        'data_entrega' => 'datetime',
        'data_recolha' => 'datetime',
        'valor' => 'decimal:2',
        'status' => RentalStatus::class,
        ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Rental $rental) {
            if (empty($rental->cliente_nome) && $rental->client_id) {
                $rental->cliente_nome = $rental->client?->nome;
            }
            if (empty($rental->endereco_entrega) && $rental->address_id) {
                $address = $rental->address;
                if ($address) {
                    $rental->endereco_entrega = sprintf(
                        '%s, %s - %s, %s/%s',
                        $address->logradouro,
                        $address->numero,
                        $address->bairro,
                        $address->cidade,
                        $address->uf
                    );
                }
            }
        });

        static::updating(function (Rental $rental) {
            if ($rental->isDirty('client_id') && $rental->client_id) {
                $rental->cliente_nome = $rental->client?->nome;
            }
            if ($rental->isDirty('address_id') && $rental->address_id) {
                $address = $rental->address;
                if ($address) {
                    $rental->endereco_entrega = sprintf(
                        '%s, %s - %s, %s/%s',
                        $address->logradouro,
                        $address->numero,
                        $address->bairro,
                        $address->cidade,
                        $address->uf
                    );
                }
            }
        });
    }

    public function getClienteNomeAttribute($value)
    {
        return $value ?? $this->client?->nome;
    }

    public function getEnderecoEntregaAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->address) {
            return sprintf(
                '%s, %s - %s, %s/%s',
                $this->address->logradouro,
                $this->address->numero,
                $this->address->bairro,
                $this->address->cidade,
                $this->address->uf
            );
        }

        return null;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_rental')
                ->withTimestamps();
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
