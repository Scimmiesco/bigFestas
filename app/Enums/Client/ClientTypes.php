<?php

namespace App\Enums\Client;

enum ClientTypes: string
{

    case PJ = 'PJ';
    case PF = 'PF';

    public function label(): string
    {
        return match($this) {
            self::PJ => 'Pesoa Jurídica',
            self::PF => 'Pessoa Física',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
