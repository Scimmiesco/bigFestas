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

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
