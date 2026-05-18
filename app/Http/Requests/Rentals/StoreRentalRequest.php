<?php

namespace App\Http\Requests\Rentals;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer essa requisição.
     */
    public function authorize(): bool
    {
        // Garante que o usuário logado pertence ao time da URL
        return $this->user()->belongsToTeam($this->route('current_team'));
    }

    /**
     * Retorna as regras de validação.
     */
    public function rules(): array
    {
        return [
            'cliente_nome' => ['required', 'string', 'max:255'],
            'endereco_entrega' => ['required', 'string', 'max:500'],
            'data_entrega' => ['required', 'date'],
            'data_recolha' => ['required', 'date', 'after:data_entrega'],
            'qtd_cadeiras' => ['required', 'integer', 'min:0'],
            'qtd_mesas' => ['required', 'integer', 'min:0'],
            'valor' => ['min:0']
        ];
    }

    /**
     * (Opcional) Traduzir as mensagens de erro caso o usuário faça algo errado
     */
    public function messages(): array
    {
        return [
            'data_recolha.after' => 'A data de recolha deve ser obrigatoriamente depois da data de entrega.',
            'qtd_cadeiras.min' => 'A quantidade de cadeiras não pode ser negativa.',
            'qtd_mesas.min' => 'A quantidade de mesas não pode ser negativa.',
            'valor.min' => 'O valor do aluguel não pode ser negativo.',
        ];
    }
}
