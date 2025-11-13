<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Se asume que el usuario autenticado tiene permiso para registrar un gasto
        // en un evento al que tiene acceso.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // PU-02: Importe (amount) debe ser un número positivo > 0.
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'payer_id' => ['required', 'exists:users,user_id'],
        ];
    }

    public function messages()
    {
        return [
            'amount.min' => 'El importe debe ser superior a 0.00€ para registrar un gasto.',
            'payer_id.required' => 'Debes indicar quién pagó el gasto.',
        ];
    }
}
