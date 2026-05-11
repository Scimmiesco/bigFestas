<?php
namespace App\Item\Enums;

enum ItemStatus: string
{
    case Disponivel = 'disponivel';
    case Alugado = 'alugado';
    case Avariado = 'avariado';
    case Manutencao = 'manutencao';
}
