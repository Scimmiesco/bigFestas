<?php

namespace App\Enums\Rental;

enum RentalStatus: string
{
    case Agendado = 'agendado';
    case EmAndamento = 'em_andamento'; // Entregue ao cliente, aguardando recolha
    case Concluido = 'concluido';      // Devolvido e finalizado
    case Cancelado = 'cancelado';

    /**
     * Retorna o nome formatado para exibir no frontend (Vue)
     */
    public function label(): string
    {
        return match($this) {
            self::Agendado => 'Agendado',
            self::EmAndamento => 'Em Andamento',
            self::Concluido => 'Concluído',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * (Opcional) Retorna uma cor do Tailwind para usar no frontend
     */
    public function color(): string
    {
        return match($this) {
            self::Agendado => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::EmAndamento => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Concluido => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Cancelado => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
