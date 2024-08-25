<?php

namespace App\Http\Requests\Admin\KnowledgeBaseController;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeBaseRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'excerpt' => 'required|string|max:255',
            'text' => 'required|string',
            'file' => 'nullable|file'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'name.string' => 'Поле "Название" должно быть строкой.',
            'name.max' => 'Поле "Название" не должно превышать 255 символов.',
            'excerpt.required' => 'Поле "Краткое описание" обязательно для заполнения.',
            'excerpt.string' => 'Поле "Краткое описание" должно быть строкой.',
            'excerpt.max' => 'Поле "Краткое описание" не должно превышать 255 символов.',
            'text.required' => 'Поле "Текст" обязательно для заполнения.',
            'text.string' => 'Поле "Текст" должно быть строкой.',
            'file.file' => 'Файл должен быть файлом'
        ];
    }
}
