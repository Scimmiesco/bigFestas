<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Enums\Address\AddressTypes;
use App\Models\Address;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class AddressController extends Controller
{
    /**
     * Lista os endereços.
     */
    public function index(Team $currentTeam): Response
    {
        // Carrega os endereços já incluindo os dados do cliente dono de cada um
        $enderecos = Address::query()
            ->with('client')
            ->latest()
            ->paginate(10);

        return Inertia::render('addresses/Index', [
            'enderecos' => $enderecos
        ]);
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create(Team $currentTeam): Response
    {
        return Inertia::render('addresses/Create', [
            // Envia a lista de clientes para o select do formulário
            'clients' => Client::query()->select('id', 'nome')->orderBy('nome')->get(),
            // Envia os tipos de endereço mapeados para o select (Ex: Residencial, Comercial)
            'enums' => [
                'address_types' => 'fasfsa'
            ]
        ]);
    }

    /**
     * Salva um novo endereço no banco de dados.
     */
    public function store(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'tipo' => 'required|string', // Ajuste a validação conforme o seu Enum
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
        ]);

        Address::create($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço cadastrado com sucesso!');
    }

    /**
     * Exibe os detalhes de um endereço específico (opcional).
     */
    public function show(Team $currentTeam, Address $address): Response
    {
        $address->load('client');

        return Inertia::render('addresses/Show', [
            'address' => $address
        ]);
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Team $currentTeam, Address $address): Response
    {
        return Inertia::render('addresses/Edit', [
            'address' => $address,
            'clients' => Client::query()->select('id', 'nome')->orderBy('nome')->get(),
            'enums' => [
                'address_types' => 'AddressTypes::options()',
            ]
        ]);
    }

    /**
     * Atualiza o endereço no banco de dados.
     */
    public function update(Request $request, Team $currentTeam, Address $address): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'tipo' => 'required|string',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
        ]);

        $address->update($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço atualizado com sucesso!');
    }

    /**
     * Remove o endereço do banco de dados.
     */
    public function destroy(Team $currentTeam, Address $address): RedirectResponse
    {
        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço removido com sucesso!');
    }
}
