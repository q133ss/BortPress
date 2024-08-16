<?php

namespace App\Http\Requests\ChatController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'text' => [
                'nullable',
                'string'
            ],
            'file' => [
                'nullable',
                'file'
            ]
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (is_null($this->input('text')) && is_null($this->file('file'))) {
                $validator->errors()->add('text', 'Введите сообщение или выберите файл.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'text.string' => 'Сообщение должно быть строкой',
            'file.file' => 'Неверный формат файла'
        ];
    }
}
