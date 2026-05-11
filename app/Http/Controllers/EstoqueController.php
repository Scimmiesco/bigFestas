<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class EstoqueController extends Controller
{
    public function index()
        {
            // Por enquanto, vamos mandar dados "fake" para testar o Inertia
            return Inertia::render('estoque/Index', [
                'mensagem' => 'Olá do Laravel para o Vue!'
            ]);
        }
}
