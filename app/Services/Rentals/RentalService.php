<?php

namespace App\Services\Rentals;

use App\Enums\Rental\RentalStatus;
use App\Models\Team;

class RentalService
{
    public function getSummary(Team $team): array
    {
        $fimDoPeriodo = now()->addDays(10)->endOfDay();

        $locacoesSemana = $team->rentals()
            ->with('items')
            ->whereIn('status', [RentalStatus::Agendado, RentalStatus::EmAndamento])
            ->where(function($query) use ($fimDoPeriodo) {
                $query->whereBetween('data_entrega', [now()->startOfDay(), $fimDoPeriodo])
                      ->orWhereBetween('data_recolha', [now()->startOfDay(), $fimDoPeriodo]);
            })
            ->get();

        return [];
    }
}
