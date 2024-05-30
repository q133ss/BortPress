<?php

namespace App\Http\Requests\RegisterController;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|max:255|regex:/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/|unique:users,phone',
            'role_id' => 'required|integer|exists:roles,id|in:1,2',
            'password' => 'required|max:255|min:8|string|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя',
            'name.string' => 'Поле имя должно быть строкой',
            'name.max' => 'Поле имя не должно превышать 255 символов',

            'email.required' => 'Укажите email',
            'email.email' => 'Поле email некорректное',
            'email.unique' => 'Пользователь с таким email уже существует',
            'email.max' => 'Поле email не должно превышать 255 символов',

            'phone.required' => 'Укажите номер телефона',
            'phone.max' => 'Поле номер телефона не должно превышать 255 символов',
            'phone.unique' => 'Пользователь с таким телефоном уже существует',
            'phone.regex' => 'Поле номер телефона должно соответствовать формату +7(999)999-99-99',

            'role_id.required' => 'Укажите роль',
            'role_id.integer' => 'Роль указанна неверно',
            'role_id.in' => 'Роль указанна неверно',
            'role_id.exists' => 'Указанной роли не существует',

            'password.required' => 'Укажите пароль',
            'password.max' => 'Поле пароль не должно превышать 255 символов',
            'password.min' => 'Поле пароль должно содержать не менее 8 символов',
            'password.string' => 'Поле пароль должно быть строкой',
            'password.confirmed' => 'Пароли не совпадают'
        ];
    }
}
