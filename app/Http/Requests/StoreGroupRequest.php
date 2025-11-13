<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Permite que cualquier usuario autenticado use este formulario
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'], // Mínimo 3 caracteres
            'description' => ['nullable', 'string', 'max:255'],
            // Asegura que el código de 10 caracteres sea único en la tabla 'groups'
            'invitation_code' => ['required', 'string', 'min:5', 'max:10', 'unique:groups,invitation_code'],
        ];
    }
}
