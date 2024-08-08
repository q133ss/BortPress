<?php

namespace App\Http\Requests\Admin\CompanyController;

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
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'inn' => 'required|string|max:12',
            'kpp' => 'required|string|max:9',
            'ogrn' => 'required|string|max:13',
            'fact_address' => 'required|string|max:255',
            'ur_address' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'site_url' => 'required|url|max:255',
            'description' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Поле user_id обязательно для заполнения.',
            'user_id.exists' => 'Указанный user_id не существует.',
            'name.required' => 'Поле name обязательно для заполнения.',
            'name.string' => 'Поле name должно быть строкой.',
            'name.max' => 'Поле name не должно превышать 255 символов.',
            'inn.required' => 'Поле inn обязательно для заполнения.',
            'inn.string' => 'Поле inn должно быть строкой.',
            'inn.max' => 'Поле inn не должно превышать 12 символов.',
            'kpp.required' => 'Поле kpp обязательно для заполнения.',
            'kpp.string' => 'Поле kpp должно быть строкой.',
            'kpp.max' => 'Поле kpp не должно превышать 9 символов.',
            'ogrn.required' => 'Поле ogrn обязательно для заполнения.',
            'ogrn.string' => 'Поле ogrn должно быть строкой.',
            'ogrn.max' => 'Поле ogrn не должно превышать 13 символов.',
            'fact_address.required' => 'Поле fact_address обязательно для заполнения.',
            'fact_address.string' => 'Поле fact_address должно быть строкой.',
            'fact_address.max' => 'Поле fact_address не должно превышать 255 символов.',
            'ur_address.required' => 'Поле ur_address обязательно для заполнения.',
            'ur_address.string' => 'Поле ur_address должно быть строкой.',
            'ur_address.max' => 'Поле ur_address не должно превышать 255 символов.',
            'region_id.required' => 'Поле region_id обязательно для заполнения.',
            'region_id.exists' => 'Указанный region_id не существует.',
            'site_url.required' => 'Поле site_url обязательно для заполнения.',
            'site_url.url' => 'Поле site_url должно быть действительным URL.',
            'site_url.max' => 'Поле site_url не должно превышать 255 символов.',
            'description.required' => 'Поле description обязательно для заполнения.',
            'description.string' => 'Поле description должно быть строкой.',
            'description.max' => 'Поле description не должно превышать 1000 символов.',
        ];
    }
}
