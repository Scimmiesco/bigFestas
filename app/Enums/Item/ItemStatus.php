<?php
namespace App\Enums\Item;

enum ItemStatus: string
{
    case Disponivel = 'disponivel';
    case Alugado = 'alugado';
    case Avariado = 'avariado';
    case Manutencao = 'manutencao';

    // Método extra para exibir o nome bonito no frontend
    public function label(): string
    {
        return match($this) {
            self::Disponivel => 'Disponível',
            self::Alugado => 'Alugado',
            self::Avariado => 'Avariado',
            self::Manutencao => 'Em Manutenção',
        };
    }
}
