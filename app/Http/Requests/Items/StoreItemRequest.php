<?php

namespace App\Http\Requests\Items;

use App\Enums\Item\ItemStatus;
use App\Enums\Item\ItemType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->belongsToTeam($this->route('current_team'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'nome' => ['required', 'string', 'max:255'],
                    'tipo' => ['required', Rule::enum(ItemType::class)],
                    'status' => ['required', Rule::enum(ItemStatus::class)],
                    'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
