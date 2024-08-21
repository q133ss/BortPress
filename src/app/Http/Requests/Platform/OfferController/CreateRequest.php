<?php

namespace App\Http\Requests\Platform\OfferController;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'name' => 'required|string',
            'type_id' => 'required|exists:types,id',
            'inventory' => 'required|exists:items,id',
            //'inventory.*' => 'required|exists:items,id',
            'pay_format' => 'required|exists:pay_formats,id',
            'region_id' => 'required|array',
            'region_id.*' => 'required|exists:regions,id',
            'budget' => 'required|integer',
            'document' => 'nullable|file',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'additional_info' => 'required|string',
            'link' => 'required|url',
            'photo' => 'nullable|file',

            'cost_by_price' => 'nullable|numeric',
            'discount_cost' => 'nullable|numeric',
            'possibility_of_extension' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название',
            'name.string' => 'Название должно быть строкой',

            'type_id.required' => 'Укажите тип',
            'type_id.exists' => 'Указанного типа не существует',

            'inventory.required' => 'Укажите товар или услугу',
            'inventory.exists' => 'Указан неверный товар или услуга',

            'inventory.*.exists' => 'Указан неверный товар или услуга',
            'inventory.*.required' => 'Укажите товар или услугу',

            'pay_format.required' => 'Укажите формат оплаты',
            'pay_format.string' => 'Формат оплаты должен быть строкой',
            'pay_format.in' => 'Формат оплаты должен быть одним из следующих: cash, barter, sliv',

            'region_id.required' => 'Укажите регионы',
            'region_id.array' => 'Регионы должны быть массивом',
            'region_id.*.required' => 'Укажите регион',
            'region_id.*.exists' => 'Указанного региона не существует',

            'budget.required' => 'Укажите бюджет',
            'budget.integer' => 'Бюджет должен быть целым числом',

            'document.required' => 'Загрузите документ',
            'document.file' => 'Документ должен быть файлом',

            'start_date.required' => 'Укажите дату начала',
            'start_date.date_format' => 'Дата начала должна быть в формате ГГГГ-ММ-ДД',
            'start_date.after_or_equal' => 'Дата начала должна быть сегодняшней или в будущем',

            'end_date.required' => 'Укажите дату окончания',
            'end_date.date_format' => 'Дата окончания должна быть в формате ГГГГ-ММ-ДД',
            'end_date.after' => 'Дата окончания должна быть позже даты начала',

            'additional_info.required' => 'Введите дополнительную информацию',
            'additional_info.string' => 'Дополнительная информация должна быть строкой',

            'link.required' => 'Введите ссылку',
            'link.url' => 'Неверный формат ссылки',

            'photo.required' => 'Загрузите фото',
            'photo.file' => 'Фото должно быть файлом',

            'cost_by_price.numeric' => 'Цена по прайсу должна быть числом',
            'discount_cost.numeric' => 'Цена со скидкой должна быть числом',
            'possibility_of_extension.boolean' => 'Возможность продления должно быть 1 или 0'
        ];
    }
}
