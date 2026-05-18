<?php

namespace App\Services\Dashboard;

use App\Enums\Item\ItemType;
use App\Enums\Rental\RentalStatus;
use App\Models\Team;
use Illuminate\Support\Carbon;

class StockResumeService
{
    public function getSummary(Team $team): array
    {
        // 1. Contagens do estoque
        $contagens = $team->items()
            ->selectRaw('tipo, status, count(*) as total')
            ->groupBy('tipo', 'status')
            ->get();

        $estoqueAgrupado = [];
        foreach ($contagens as $linha) {
            $tipoString = $linha->tipo->value ?? $linha->tipo;
            $status = $linha->status->value ?? $linha->status;
            $tipoEnum = ItemType::tryFrom($tipoString);
            $nomeOficial = $tipoEnum ? $tipoEnum->label() : ucfirst($tipoString) . 's';

            if (!isset($estoqueAgrupado[$tipoString])) {
                $estoqueAgrupado[$tipoString] = [
                    'tipo' => $tipoString,
                    'nome' => $nomeOficial,
                    'disponivel' => 0,
                    'alugado' => 0,
                    'manutencao' => 0,
                    'total' => 0,
                ];
            }
            if (isset($estoqueAgrupado[$tipoString][$status])) {
                $estoqueAgrupado[$tipoString][$status] = (int) $linha->total;
            }
            $estoqueAgrupado[$tipoString]['total'] += (int) $linha->total;
        }

        $cadeirasDisponiveis = $estoqueAgrupado['cadeira']['disponivel'] ?? 0;
        $mesasDisponiveis = $estoqueAgrupado['mesa']['disponivel'] ?? 0;
        $jogosDisponiveis = min(floor($cadeirasDisponiveis / 4), $mesasDisponiveis);
        $cadeirasAvulsas = $cadeirasDisponiveis - ($jogosDisponiveis * 4);

        // 2. BUSCA DA AGENDA SEMANAL (Próximos 7 dias)
        $fimDaSemana = now()->addDays(7)->endOfDay();

        // 👇 OTIMIZAÇÃO: "with('items')" evita o problema de N+1 queries no banco de dados
        $locacoesSemana = $team->rentals()
            ->with('items')
            ->whereIn('status', [RentalStatus::Agendado, RentalStatus::EmAndamento])
            ->where(function($query) use ($fimDaSemana) {
                $query->whereBetween('data_entrega', [now()->startOfDay(), $fimDaSemana])
                      ->orWhereBetween('data_recolha', [now()->startOfDay(), $fimDaSemana]);
            })
            ->get();

        $compromissosAlineados = [];

        foreach ($locacoesSemana as $locacao) {
                    // Usando 'where()' da Collection ao invés de bater no banco de novo
                    $mesas = $locacao->items->where('tipo', 'mesa')->count();
                    $cadeiras = $locacao->items->where('tipo', 'cadeira')->count();
                    $carga = "{$mesas}M / {$cadeiras}C";

                    // Se tem entrega agendada nesta semana, adiciona como entrega
                    if ($locacao->data_entrega && $locacao->data_entrega->isBetween(now()->startOfDay(), $fimDaSemana)) {
                        $compromissosAlineados[] = $this->formatarCompromisso(
                            $locacao,
                            $locacao->data_entrega,
                            'entrega',
                            $carga,

                        );
                    }

                    // Se tem recolha agendada nesta semana, adiciona como recolha
                    if ($locacao->data_recolha && $locacao->data_recolha->isBetween(now()->startOfDay(), $fimDaSemana)) {
                        $compromissosAlineados[] = $this->formatarCompromisso(
                            $locacao,
                            $locacao->data_recolha,
                            'recolha',
                            $carga,
                        );
                    }
                }

        // Ordena os compromissos cronologicamente por timestamp
        usort($compromissosAlineados, function($a, $b) {
            return strcmp($a['timestamp'], $b['timestamp']);
        });

        // Agrupa por Dia (Ex: "Hoje", "Amanhã", "Segunda-feira")
        $agendaAgrupada = [];
        foreach ($compromissosAlineados as $comp) {
            $grupo = $comp['dia_label'];
            if (!isset($agendaAgrupada[$grupo])) {
                $agendaAgrupada[$grupo] = [];
            }
            $agendaAgrupada[$grupo][] = $comp;
        }

        return [
            'jogos_disponiveis' => (int) $jogosDisponiveis,
            'cadeiras_avulsas' => (int)$cadeirasAvulsas,
            'estoque' => array_values($estoqueAgrupado),
            'agenda_semanal' => $agendaAgrupada
        ];
    }

    private function formatarCompromisso($locacao, \DateTimeInterface $dataAlvo, string $operacao, string $carga): array
    {
        $dataAlvo = \Illuminate\Support\Carbon::instance($dataAlvo)->locale('pt_BR');

        if ($dataAlvo->isToday()) {
            $diaLabel = 'Hoje';
        } elseif ($dataAlvo->isTomorrow()) {
            $diaLabel = 'Amanhã';
        } else {
            $diaLabel = ucfirst($dataAlvo->translatedFormat('l (d/m)'));
        }

        return [
            'id' => $locacao->id,
            'operacao' => $operacao,
            'cliente' => $locacao->cliente_nome,
            'horario' => $dataAlvo->format('H:i'),
            'endereco' => $locacao->endereco_entrega,
            'quantidade' => $carga,
            'valor' => $locacao->valor, // 👈 Adicionado aqui
            'dia_label' => $diaLabel,
            'timestamp' => $dataAlvo->toDateTimeString()
        ];
    }
}
