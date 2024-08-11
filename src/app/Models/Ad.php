<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Ad extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'pay_format' => 'array',
        'inventory' => 'array',
    ];

    public function __getPayFormatAttribute($value)
    {
        // Проверяем, является ли значение валидным JSON
        if ($this->isJson($value)) {
            $decodedValue = json_decode($value, true);

            // Если декодированное значение является строкой, снова декодируем его
            if (is_string($decodedValue) && $this->isJson($decodedValue)) {
                return json_decode($decodedValue, true);
            }

            return $decodedValue;
        }

        return $value;
    }

    public function __getInventoryAttribute($value)
    {
        // Проверяем, является ли значение валидным JSON
        if ($this->isJson($value)) {
            $decodedValue = json_decode($value, true);

            // Если декодированное значение является строкой, снова декодируем его
            if (is_string($decodedValue) && $this->isJson($decodedValue)) {
                return json_decode($decodedValue, true);
            }

            return $decodedValue;
        }

        return $value;
    }

    // Метод для проверки, является ли строка валидным JSON
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function setPayFormatAttribute($value)
    {
        $this->attributes['pay_format'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setInventoryAttribute($value)
    {
        $this->attributes['inventory'] = is_array($value) ? json_encode($value) : $value;
    }

    public function photo(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        $owner = $this->owner()->first();
        return $this->morphOne(File::class, 'fileable')
            ->where('category', 'photo')
            ->withDefault(function () use ($owner) {
                return File::where([
                    'fileable_id' => $owner->company?->id,
                    'fileable_type' => 'App\Models\Company',
                    'category' => 'logo'
                ])->first();
            });
    }

    public function document(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->where('category', 'document');
    }

    public function scopeWithFilter($query, Request $request)
    {
        return $query
            ->when(
                $request->query('type_id'),
                function (Builder $query, $type_id) {
                    return $query->where('type_id', $type_id);
                }
            )
            ->when(
                $request->query('inventory'),
                function (Builder $query, $inventory) {
                    $ids = explode(',', $inventory); // Разбиваем строку на массив ID
                    return $query->where(function ($query) use ($ids) {
                        foreach ($ids as $id) {
                            $query->orWhereJsonContains('inventory', (int) $id);
                        }
                    });
                }
            )
            ->when(
                $request->query('region_id'),
                function (Builder $query, $region_id) {
                    return $query->where('region_id', $region_id);
                }
            )
            ->when(
                $request->query('pay_format'),
                function (Builder $query, $pay_format) {
                    $ids = explode(',', $pay_format); // Разбиваем строку на массив ID
                    return $query->where(function ($query) use ($ids) {
                        foreach ($ids as $id) {
                            $query->orWhereJsonContains('pay_format', (int) $id);
                        }
                    });
                }
            )
            ->when(
                $request->query('budget_from'),
                function (Builder $query, $budget_from) {
                    return $query->where('budget', '>=', $budget_from);
                }
            )
            ->when(
                $request->query('budget_to'),
                function (Builder $query, $budget_to) {
                    return $query->where('budget', '<=', $budget_to);
                }
            )
            ->when(
                $request->query('period_from'),
                function (Builder $query, $period_from) {
                    return $query->where('start_date', '>=', $period_from);
                }
            )
            ->when(
                $request->query('period_to'),
                function (Builder $query, $period_to) {
                    return $query->where('end_date', '<=', $period_to);
                }
            )
            ->when(
                $request->query('hot_offers'),
                function (Builder $query, $hot_offers) {
                    $sliv_id = DB::table('pay_formats')->where('slug', 'sliv')->pluck('id')->first();
                    return $query->whereJsonContains('pay_format', $sliv_id);
                }
            );
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function item()
    {
        // Обработка случая, когда inventory может быть null
        $inventoryIds = $this->inventory ?? [];

        return \App\Models\Item::whereIn('id', $inventoryIds)->get();
    }

    public function getType()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }

    public function getTypeIdAttribute($value)
    {
        return Type::find($value);
    }

    public function getInventoryAttribute($value)
    {
        $inventoryIds = json_decode($value) ?? [];

        return Item::whereIn('id', $inventoryIds)->get();
    }

    public function getPayFormatAttribute($value)
    {
        $payFormatIds = json_decode($value) ?? [];
        return PayFormat::whereIn('id', $payFormatIds)->get();
    }

    public function getRegionIdAttribute($value)
    {
        return Region::find($value);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type_id->toArray(),
            'inventory' => $this->inventory->toArray(),
            'pay_format' => $this->pay_format,
            'region' => $this->region_id,
            'budget' => $this->budget,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_unique' => $this->is_unique,
            'user_id' => $this->user_id,
            'additional_info' => $this->additional_info,
            'link' => $this->link,
            'is_offer' => $this->is_offer,
            'is_selling' => $this->is_selling,
            'is_archive' => $this->is_archive,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}
