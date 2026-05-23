<?php
namespace App\Enums\Item;

enum ItemType: string
{
    case Cadeira = 'cadeira';
    case Mesa = 'mesa';
    case Cooler = 'cooler';
    case Tenda = 'tenda';

    public function label(): string
    {
        return match($this) {
            self::Cooler => 'Coolers',
            self::Mesa => 'Mesas',
            self::Cadeira => 'Cadeiras',
            self::Tenda => 'Tendas',
        };
    }
}
