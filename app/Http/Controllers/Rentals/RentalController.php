<?php

namespace App\Http\Controllers\Rentals;

use App\Enums\Item\ItemStatus;
use App\Enums\Item\ItemType;
use App\Enums\Rental\RentalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rentals\StoreRentalRequest;
use App\Models\Rental;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RentalController extends Controller
{
    public function index(Team $current_team)
    {
        // Puxa as locações do time, ordenando da data de entrega mais próxima para a mais distante
        $locacoes = $current_team->rentals()
            ->orderBy('data_entrega', 'asc')
            ->get()
            ->map(fn ($rental) => [
                'id' => $rental->id,
                'cliente_nome' => $rental->cliente_nome,
                'endereco_entrega' => $rental->endereco_entrega,
                'data_entrega' => $rental->data_entrega,
                'data_recolha' => $rental->data_recolha,
                'status' => $rental->status,
            ]);

        return Inertia::render('rentals/Index', [
            'locacoes' => $locacoes
        ]);
    }
    public function create(Team $current_team)
        {
            // 1. Uso dos Enums nas consultas: código mais seguro e sem risco de erro de digitação
            $estoqueDisponivel = [
                'cadeiras' => $current_team->items()
                    ->where('tipo', ItemType::Cadeira) // Adapte para a sintaxe exata do seu Enum
                    ->where('status', ItemStatus::Disponivel)
                    ->count(),

                'mesas' => $current_team->items()
                    ->where('tipo', ItemType::Mesa)
                    ->where('status', ItemStatus::Disponivel)
                    ->count(),
            ];

            // Certifique-se de que a capitalização (Rentals) bate com o nome da pasta no Vue
            return Inertia::render('rentals/Create', [
                'estoqueDisponivel' => $estoqueDisponivel
            ]);
        }

        public function store(StoreRentalRequest $request, Team $current_team)
        {
            $validated = $request->validated();

            DB::transaction(function () use ($validated, $current_team) {

                // A. Cria a Locação
                $rental = $current_team->rentals()->create([
                    'cliente_nome' => $validated['cliente_nome'],
                    'endereco_entrega' => $validated['endereco_entrega'],
                    'data_entrega' => $validated['data_entrega'],
                    'data_recolha' => $validated['data_recolha'],
                    'valor' => $validated['valor'],
                    'status' => RentalStatus::Agendado, // Dica: Se você criar um RentalStatus Enum no futuro, atualize aqui também!
                ]);

                // B. Função auxiliar para NÃO repetir código ao separar os itens
                $separarItensParaLocacao = function ($tipoEnum, $quantidadeDesejada) use ($current_team, $rental) {
                    if ($quantidadeDesejada > 0) {
                        $itens = $current_team->items()
                            ->where('tipo', $tipoEnum)
                            ->where('status', ItemStatus::Disponivel)
                            ->limit($quantidadeDesejada)
                            ->get();

                        $rental->items()->attach($itens->pluck('id'));

                        $current_team->items()
                            ->whereIn('id', $itens->pluck('id'))
                            ->update(['status' => ItemStatus::Alugado]);
                    }
                };

                // C. Chamamos a mesma função para Cadeiras e Mesas
                $separarItensParaLocacao(ItemType::Cadeira, $validated['qtd_cadeiras']);
                $separarItensParaLocacao(ItemType::Mesa, $validated['qtd_mesas']);
            });

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Locação agendada com sucesso!']);

            // Redirecionando para a index de locações (melhor fluxo de UX)
            return redirect()->route('rentals.index', ['current_team' => $current_team->slug]);
        }

        public function show(Team $current_team, $id)   {

            $rental = $current_team->rentals()->with('items')->findOrFail($id);

            if (request()->wantsJson()) {
                return response()->json([
                    'rental' => $rental
                ]);
            }

            return inertia('rentals/Show', [
                'rental' => $rental,
            ]);
        }
}
