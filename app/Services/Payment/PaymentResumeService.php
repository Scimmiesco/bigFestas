<?php

namespace App\Services\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Models\Team;
use Carbon\Carbon;

class PaymentResumeService
{
    public function getSummary(Team $team): array
        {
            $pagamentos = $team->payment();

            $totalPago = $pagamentos
                        ->where('payments.status', PaymentStatus::Realizado)
                        ->sum('payments.valor');

            $valorTotalLocacoes = $team->rentals()->sum('valor');

            $aReceber = $valorTotalLocacoes - $totalPago;

            $emAtraso =  $pagamentos
                        ->where('payments.status', PaymentStatus::Pendente)
                        ->where('data_vencimento', '<', Carbon::now()->startOfDay())
                        ->sum('payments.valor');

            return [
                        'faturamento_total' => (float) $totalPago,
                        'a_receber'         => (float) max(0, $aReceber),
                        'em_atraso'         => (float) $emAtraso,
                    ];
        }

}
