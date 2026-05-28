<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct()
    {
        //
    }

    // Corrigi o pequeno erro de digitação ($curentTeam para $currentTeam)
    public function index(Team $currentTeam): Response
    {
        /**
         * Busca os clientes ordenados pelos mais recentes e pagina de 10 em 10.
         * * NOTA DE ARQUITETURA: Se os seus clientes forem separados por "Time",
         * o ideal aqui seria buscar através do relacionamento:
         * $clientes = $currentTeam->clients()->latest()->paginate(10);
         */
        $clientes = Client::query()->latest()->paginate(10);

        return Inertia::render('clients/Index', [
            'clientes' => $clientes
        ]);
    }
}
