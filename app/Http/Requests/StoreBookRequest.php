<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'min:3', 'max:255'],
            'author'       => ['required', 'string', 'min:3', 'max:255'],
            'description'  => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'O título é obrigatório.',
            'title.min'          => 'O título deve ter no mínimo 3 caracteres.',
            'title.max'          => 'O título deve ter no máximo 255 caracteres.',
            'author.required'    => 'O autor é obrigatório.',
            'author.min'         => 'O autor deve ter no mínimo 3 caracteres.',
            'author.max'         => 'O autor deve ter no máximo 255 caracteres.',
            'description.min'    => 'A descrição deve ter no mínimo 3 caracteres.',
            'description.max'    => 'A descrição deve ter no máximo 255 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
        ];
    }

    /**
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erro de validação.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
