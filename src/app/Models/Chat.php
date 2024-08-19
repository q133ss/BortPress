<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'chat_id', 'id')->latestOfMany();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function ad()
    {
        return $this->hasOne(Ad::class, 'id', 'ad_id');
    }
}
