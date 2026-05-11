<?php
namespace App\Enums\Item;

enum ItemStatus: string
{
    case Disponível = 'disponivel';
    case Alugado = 'alugado';
    case Avariado = 'avariado';
    case Manutenção = 'manutencao';
}
