<?php

namespace App\Models;

use App\Enums\Payment\PaymentMethods;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'rental_id', 'valor', 'metodo',
        'status', 'data_vencimento', 'data_pagamento',
    ];

    protected $casts = [
    'data_vencimento' => 'date',
    'data_pagamento' => 'datetime',
    'status' => PaymentStatus::class,
    'metodo' => PaymentMethods::class,
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}
