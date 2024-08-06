<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Ad extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'pay_format' => 'array',
    ];

    public function getPayFormatAttribute($value)
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
//                $request->query('inventory'),
//                function (Builder $query, $inventory) {
//                    return $query->where('inventory', $inventory);
//                }
                $request->query('inventory'),
                function (Builder $query, $inventory) {
                    $inventoryArray = is_array($inventory) ? $inventory : explode(',', $inventory);
                    return $query->whereIn('inventory', $inventoryArray);
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
                    return $query->whereJsonContains('pay_format', $pay_format);
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
            );
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}
