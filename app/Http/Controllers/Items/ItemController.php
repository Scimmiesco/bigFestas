<?php

namespace App\Http\Controllers\Items;

use App\Enums\Item\ItemStatus;
use App\Enums\Item\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Items\StoreItemRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Team $current_team): Response
    {
        $items = $current_team->items()->latest()->get()->map(fn ($item) => [
            'id' => $item->id,
            'nome' => $item->nome,
            'tipo' => $item->tipo->value ?? $item->tipo->name,
            'status' => $item->status->value ?? $item->status->name,
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
            $current_team->items()->create($request->validated());

            Inertia::flash('toast', ['type' => 'success', 'message' => __('Item criado com sucesso.')]);

            return redirect()->route('items.index', ['current_team' => $current_team->slug]);
        }
}
