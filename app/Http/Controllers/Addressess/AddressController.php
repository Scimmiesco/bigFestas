<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
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
}
