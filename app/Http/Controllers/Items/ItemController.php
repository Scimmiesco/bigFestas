<?php

namespace App\Http\Controllers\Items;

use App\Enums\Item\ItemStatus;
use App\Enums\Item\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Items\StoreItemRequest;
use App\Models\Team;
// Adicione a importação do Model Item se for usar Type Hinting, ou busque pelo ID
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    /**
     * Lista os itens com paginação.
     */
    public function index(Team $current_team): Response
    {
        // Usamos paginate(15) e through() para formatar a coleção sem quebrar a paginação
        $items = $current_team->items()
            ->latest()
            ->paginate(15)
            ->through(fn ($item) => [
                'id' => $item->id,
                'nome' => $item->nome,
                'tipo' => $item->tipo->value ?? $item->tipo,
                'status' => $item->status->value ?? $item->status,
            ]);

        return Inertia::render('items/Index', [
            'estoque' => $items,
        ]);
    }

    /**
     * Exibe a tela do formulário de criação.
     */
    public function create(Team $current_team): Response
    {
        return Inertia::render('items/Create', [
            'itemTypes' => ItemType::cases(),
            'itemStatuses' => ItemStatus::cases(),
        ]);
    }

    /**
     * Salva o item no banco de dados.
     */
    public function store(StoreItemRequest $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validated();

        $quantidade = $validated['quantidade'] ?? 1;
        unset($validated['quantidade']);

        DB::transaction(function () use ($current_team, $validated, $quantidade) {
            for ($i = 0; $i < $quantidade; $i++) {
                $current_team->items()->create($validated);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "{$quantidade} item(ns) criado(s) com sucesso!"
        ]);

        return redirect()->route('items.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Exibe a tela de edição do item.
     */
    public function edit(Team $current_team, $itemId): Response
    {
        // Busca o item garantindo que pertence ao time atual
        $item = $current_team->items()->findOrFail($itemId);

        return Inertia::render('items/Edit', [
            'item' => [
                'id' => $item->id,
                'nome' => $item->nome,
                'tipo' => $item->tipo->value ?? $item->tipo,
                'status' => $item->status->value ?? $item->status,
                'observacoes' => $item->observacoes,
            ],
            'itemTypes' => ItemType::cases(),
            'itemStatuses' => ItemStatus::cases(),
        ]);
    }

    /**
     * Atualiza os dados do item.
     */
    public function update(Request $request, Team $current_team, $itemId): RedirectResponse
    {
        $item = $current_team->items()->findOrFail($itemId);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string'],
            'status' => ['required', 'string'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $item->update($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Item atualizado com sucesso!"
        ]);

        return redirect()->route('items.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Remove um item do banco de dados.
     */
    public function destroy(Team $current_team, $itemId): RedirectResponse
    {
        $item = $current_team->items()->findOrFail($itemId);

        $item->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Item excluído com sucesso!"
        ]);

        // Retorna back() para manter o usuário na mesma página em que estava (útil com paginação)
        return redirect()->back();
    }

    public function bulkUpdate(Request $request, Team $current_team): RedirectResponse
        {
            // Valida se os IDs existem e se pertencem ao time atual
            $validated = $request->validate([
                'ids' => ['required', 'array', 'min:1'],
                'ids.*' => ['integer', 'exists:items,id'],
                'status' => ['required', 'string'], // Adicione validação do Enum aqui se necessário
            ]);

            // Atualiza apenas os itens que pertencem ao time logado
            $current_team->items()
                ->whereIn('id', $validated['ids'])
                ->update([
                    'status' => $validated['status']
                ]);

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => count($validated['ids']) . " item(ns) atualizado(s) com sucesso!"
            ]);

            return redirect()->back();
        }
}
