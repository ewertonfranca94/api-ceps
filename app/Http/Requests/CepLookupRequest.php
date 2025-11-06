<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CepLookupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            //
            'cep' => ['required', 'regex:/^\d{5}-?\d{3}$/'],
        ];
    }
    protected function prepareForValidation(): void
    {
        // Captura o parâmetro da rota e remove tudo que não for número
        $this->merge([
            'cep' => preg_replace('/\D/', '', $this->route('cep')),
        ]);
    }
    public function messages(): array
    {
        return [
            'cep.regex' => 'O CEP informado é inválido. Use 8 Dígitos (com ou sem hífen)',
        ];
    }
    protected function failedValidation(Validator $validator): void 
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422)
        );    
    }
}
