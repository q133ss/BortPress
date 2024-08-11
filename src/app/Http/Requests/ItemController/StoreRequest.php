<?php

namespace App\Http\Requests\ItemController;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'string|required|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Название должно быть строкой',
            'name.required' => 'Введите название',
            'name.max' => 'Название не должно превышать 255 символов'
        ];
    }
}
