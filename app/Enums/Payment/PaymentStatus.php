<?php

namespace App\Enums\Payment;

enum PaymentStatus: string
{
    case Pendente = 'pendente';
    case Estornado = 'estornado';
    case Cancelado = 'cancelado';
    case Realizado = 'realizado';

    public function label(): string
    {
        return match($this) {
            self::Pendente => 'Pendente',
            self::Estornado => 'Estornado',
            self::Cancelado => 'Cancelado',
            self::Realizado => 'Realizado',
        };
    }
}
