<?php

namespace App\Enums\Payment;

enum PaymentMethods: string
{
    case Pix = 'pix';
    case Credito = 'credito';
    case Debito = 'debito';
    case Dinheiro = 'dinheiro';


    public function label(): string
    {
        return match($this) {
            self::Pix => 'PIX',
            self::Credito => 'Crédito',
            self::Debito => 'Débito',
            self::Dinheiro => 'Dinheiro',
        };
    }
}
