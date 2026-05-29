<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Enums\Client\ClientTypes;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Exibe a lista de clientes.
     */
    public function index(Team $currentTeam): Response
    {
        /**
         * NOTA DE ARQUITETURA: Se os clientes pertencem a um time específico,
         * o ideal é: $clientes = $currentTeam->clients()->latest()->paginate(10);
         */
        $clientes = Client::query()->latest()->paginate(10);

        return Inertia::render('clients/Index', [
            'clientes' => $clientes
        ]);
    }

    public function create(Team $currentTeam): Response
        {
            return Inertia::render('clients/Create', [
                'enums' => [
                    // Aqui você simula (ou substitui pelo seu Enum real no futuro)
                    'client_types' => [
                        'PF' => 'Pessoa Física',
                        'PJ' => 'Pessoa Jurídica',
                    ]
                ]
            ]);
        }
    /**
     * Salva o novo cliente no banco de dados.
     */
    public function store(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            // Valida se o valor enviado existe dentro do seu Enum
            'natureza_juridica' => ['required', Rule::enum(ClientTypes::class)],
            // Garante que o CPF/CNPJ seja único na tabela clients
            'cpf_cnpj' => ['required', 'string', 'max:20', 'unique:clients,cpf_cnpj'],
        ]);

        // Se houver relação com Time, adicione: $validated['team_id'] = $currentTeam->id;
        // Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Exibe os detalhes de um cliente específico.
     */
    public function show(Team $currentTeam, Client $client): Response
    {
        // Carrega os endereços relacionados para exibir na página de detalhes, se necessário
        $client->load('addresses');

        return Inertia::render('clients/Show', [
            'client' => $client
        ]);
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Team $currentTeam, Client $client): Response
    {
        return Inertia::render('clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Atualiza os dados do cliente no banco de dados.
     */
    public function update(Request $request, Team $currentTeam, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'natureza_juridica' => ['required', Rule::enum(ClientTypes::class)],
            // Ignora o ID do cliente atual na validação de "unique" para permitir que ele mantenha o próprio documento
            'cpf_cnpj' => ['required', 'string', 'max:20', Rule::unique('clients', 'cpf_cnpj')->ignore($client->id)],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove o cliente do banco de dados.
     */
    public function destroy(Team $currentTeam, Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}
