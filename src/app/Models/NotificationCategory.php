<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'category_id', 'id');
    }
}
