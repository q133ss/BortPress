<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Type extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Type::class, 'parent_id', 'id');
    }

    public function scopeWithFiler($query, Request $request)
    {
        return $query->when(
            $request->query('parent_id', function (Builder $query, $parent_id){
                return $query->where('parent_id', $parent_id);
            })
        );
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);

            // Ensure slug is unique
            $slug = $model->slug;
            $counter = 1;
            while (self::where('slug', $slug)->exists()) {
                $slug = $model->slug . '-' . $counter;
                $counter++;
            }
            $model->slug = $slug;
        });
    }
}
