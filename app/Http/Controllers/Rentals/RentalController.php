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
            // 1. Uso dinâmico do Enum para carregar os itens em estoque disponíveis
            $estoqueDisponivel = collect(ItemType::cases())->map(function ($case) use ($current_team) {
                return [
                    'type' => $case->value,
                    'label' => $case->label(),
                    'available' => $current_team->items()
                        ->where('tipo', $case)
                        ->where('status', ItemStatus::Disponivel)
                        ->count()
                ];
            });

            // Certifique-se de que a capitalização (Rentals) bate com o nome da pasta no Vue
            return Inertia::render('rentals/Create', [
                'estoqueDisponivel' => $estoqueDisponivel,
                'clients' => \App\Models\Client::with('addresses')->orderBy('nome')->get()
            ]);
        }

        public function store(StoreRentalRequest $request, Team $current_team)
        {
            $validated = $request->validated();

            DB::transaction(function () use ($validated, $current_team) {

                // A. Cria a Locação
                $rental = $current_team->rentals()->create([
                    'client_id' => $validated['client_id'],
                    'address_id' => $validated['address_id'],
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

                // C. Iterar sobre todos os itens submetidos de forma dinâmica
                if (isset($validated['items']) && is_array($validated['items'])) {
                    foreach ($validated['items'] as $typeValue => $quantidadeDesejada) {
                        $enumCase = ItemType::tryFrom($typeValue);
                        if ($enumCase && $quantidadeDesejada > 0) {
                            $separarItensParaLocacao($enumCase, $quantidadeDesejada);
                        }
                    }
                }
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
