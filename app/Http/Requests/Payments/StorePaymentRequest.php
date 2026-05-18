<?php

namespace App\Http\Requests\Payments;

use App\Enums\Payment\PaymentMethods;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determina se o usuário tem permissão para fazer essa requisição.
     */
    public function authorize(): bool
    {
        // Retorne true, assumindo que você já bloqueia usuários não logados nas rotas
        return true;
    }

    /**
     * As regras de validação.
     */
    public function rules(): array
    {
        return [
            'rental_id'        => ['required', 'exists:rentals,id'],
            'valor'            => ['required', 'numeric', 'min:0'],
            'metodo'           => ['required', Rule::enum(PaymentMethods::class)],
            'status'           => ['required', Rule::enum(PaymentStatus::class)],
            'data_vencimento'  => ['required', 'date'],
            'data_pagamento'   => ['nullable', 'date'],
        ];
    }

    /**
     * (Opcional) Mensagens de erro traduzidas/personalizadas.
     */
    public function messages(): array
    {
        return [
            'rental_id.required' => 'Por favor, selecione uma locação.',
            'rental_id.exists'   => 'A locação selecionada não existe.',
            'valor.required'     => 'O valor do pagamento é obrigatório.',
            'valor.min'          => 'O valor não pode ser negativo.',
        ];
    }

    /**
     * Manipula os dados ANTES de validar.
     * Aqui podemos colocar aquela regra da data nula caso o status seja pendente!
     */
    protected function prepareForValidation(): void
    {
        if ($this->status === 'pendente') {
            $this->merge([
                'data_pagamento' => null,
            ]);
        }
    }
}
