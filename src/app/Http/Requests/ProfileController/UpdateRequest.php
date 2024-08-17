<?php

namespace App\Http\Requests\ProfileController;

use App\Models\Role;
use App\Models\Type;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $isAdv = false;
        if(Auth()->user()->role_id == Role::where('slug', 'advertiser')->pluck('id')->first())
        {
            $isAdv = true;
        }

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth()->id())
            ],
            'phone' => [
                'required',
                'regex:/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/',
                'max:255',
                Rule::unique('users', 'phone')->ignore(Auth()->id())
            ],
            'password' => 'nullable|string|min:8|max:255',

            'documents' => 'nullable|file',
            'logo' => 'nullable|file',

            'company_name' => 'nullable|string|max:255',
            'inn' => 'nullable|string|max:255',
            'kpp' => 'nullable|string|max:255',
            'ogrn' => 'nullable|string|max:255',
            'fact_address' => 'nullable|string|max:255',
            'ur_address' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'site_url' => 'nullable|url',
            'description' => 'nullable|string',
            'types' => 'required|array',
            'types.*' => [
                'exists:types,id',
                function(string $attribute, mixed $value, Closure $fail): void
                {
                    $parent = Type::where('id',$value)->pluck('parent_id')->first();
                    if($parent != null){
                        $fail('Указан неверный формат рекламы');
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя',
            'name.string' => 'Имя должно быть строкой',
            'name.max' => 'Имя не должно превышать 255 символов',

            'email.required' => 'Укажите email',
            'email.email' => 'Поле email неверное',
            'email.max' => 'Email не должен превышать 255 символов',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',

            'phone.required' => 'Укажите номер телефона',
            'phone.regex' => 'Номер телефона должен соответствовать формату +7(999)999-99-99',
            'phone.max' => 'Номер телефона не должен превышать 255 символов',
            'phone.unique' => 'Пользователь с таким номером телефона уже зарегистрирован',

            'password.nullable' => 'Пароль не обязателен к заполнению',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать не менее 8 символов',
            'password.max' => 'Пароль не должен превышать 255 символов',

            'documents.nullable' => 'Документы не обязательны к заполнению',
            'documents.file' => 'Документы должны быть файлом',
            'logo.nullable' => 'Документы не обязательны к заполнению',
            'logo.file' => 'Документы должны быть файлом',

            'company_name.nullable' => 'Название компании не обязательно к заполнению',
            'company_name.string' => 'Название компании должно быть строкой',
            'company_name.max' => 'Название компании не должно превышать 255 символов',

            'inn.nullable' => 'ИНН не обязателен к заполнению',
            'inn.string' => 'ИНН должен быть строкой',
            'inn.max' => 'ИНН не должен превышать 255 символов',

            'kpp.nullable' => 'КПП не обязателен к заполнению',
            'kpp.string' => 'КПП должен быть строкой',
            'kpp.max' => 'КПП не должен превышать 255 символов',

            'ogrn.nullable' => 'ОГРН не обязателен к заполнению',
            'ogrn.string' => 'ОГРН должен быть строкой',
            'ogrn.max' => 'ОГРН не должен превышать 255 символов',

            'fact_address.nullable' => 'Фактический адрес не обязателен к заполнению',
            'fact_address.string' => 'Фактический адрес должен быть строкой',
            'fact_address.max' => 'Фактический адрес не должен превышать 255 символов',

            'ur_address.nullable' => 'Юридический адрес не обязателен к заполнению',
            'ur_address.string' => 'Юридический адрес должен быть строкой',
            'ur_address.max' => 'Юридический адрес не должен превышать 255 символов',

            'region_id.nullable' => 'Регион не обязателен к заполнению',
            'region_id.exists' => 'Выбранного региона не существует',

            'site_url.nullable' => 'Адрес сайта не обязателен к заполнению',
            'site_url.url' => 'Поле адрес сайта должно содержать URL',

            'description.nullable' => 'Описание не обязательно к заполнению',
            'description.string' => 'Описание должно быть строкой',

            'types.required' => 'Укажите формат рекламы',
            'types.array' => 'Формат рекламы должен быть массивом',
            'types.*.exists' => 'Указанного формата рекламы не существует'
        ];
    }
}
