<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendAiMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Veuillez saisir un message.',
            'message.max' => 'Le message ne peut pas dépasser 5000 caractères.',
        ];
    }
}
