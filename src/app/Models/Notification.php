<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['text'];

    public function category()
    {
        return $this->hasOne(NotificationCategory::class, 'id', 'category_id');
    }
}
