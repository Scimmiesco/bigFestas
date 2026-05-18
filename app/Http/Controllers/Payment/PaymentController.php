<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Team; // Necessário por causa da rota baseada em times
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse; // 👈 Para tipar o retorno do redirect

class PaymentController extends Controller
{
    // 1. Método para listar a tabela
    public function index(Team $current_team): Response
    {
        $pagamentos = Payment::with('rental')->latest()->get();

        return Inertia::render('payments/Index', [
            'pagamentos' => $pagamentos,
        ]);
    }

    // 2. Método para abrir a tela do formulário
    public function create(Team $current_team): Response
    {
        // Pega apenas os campos necessários (id e cliente_nome) para não pesar a query
        $locacoes = $current_team->rentals()->latest()->get(['id', 'cliente_nome']);

        return Inertia::render('payments/Create', [
            'locacoes' => $locacoes,
        ]);
    }

    public function store(StorePaymentRequest $request, Team $current_team): RedirectResponse
        {
            // Se o código chegou até aqui, é porque a validação do StorePaymentRequest passou com sucesso!

            // Pega os dados já validados e limpos
            $validated = $request->validated();

            // Salva no banco
            Payment::create($validated);

            // Redireciona
            return redirect()->route('payment.index', ['current_team' => $current_team->slug])
                ->with('success', 'Pagamento registrado com sucesso!');
        }
}
