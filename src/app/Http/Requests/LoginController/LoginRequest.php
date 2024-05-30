<?php

namespace App\Http\Requests\LoginController;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                function(string $attribute, mixed $value, Closure $fail): void
                {
                    $password = User::where('email', $this->email)->pluck('password')->first();
                    if(!Hash::check($value,$password)){
                        $fail('Неверный email или пароль');
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Укажите email',
            'email.email' => 'Email указан неверно',
            'email.exists' => 'Неверный email или пароль',

            'password.required' => 'Укажите пароль',
            'password.string' => 'Пароль должен быть строкой'
        ];
    }
}
