<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function photo(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->where('category', 'photo');
    }

    public function document(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->where('category', 'document');
    }
}
