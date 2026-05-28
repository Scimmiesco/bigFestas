<?php

namespace App\Enums\Address;

enum AddressTypes: string
{

    case Residencial = 'residencial';
    case Faturamento = 'faturamento';
    case Locacao = 'locacao';

    public function label(): string
    {
        return match($this) {
            self::Residencial => 'Residencial',
            self::Faturamento => 'Faturamento',
            self::Locacao => 'Locação',
        };
    }
}
