<?php

namespace App\Http\Requests\ChatController;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
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
            'text' => 'required|string',
            'file' => 'nullable|file'
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'Введите сообщение',
            'text.string' => 'Сообщение должно быть строкой',
            'file.file' => 'Неверный формат файла'
        ];
    }
}
