<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\Chat;

class ChatService
{
    public function create(int $adv_id)
    {
        $ad = Ad::findOrFail($adv_id);

        $userId = $ad->user_id;
        $currentUserId = Auth()->id();

        if($userId == $currentUserId){
            return Response()->json(['message' => 'Нельзя создать чат с самим собой', 'errors' => ['error' => 'Нельзя создать чат с самим собой']], 422);
        }

        $chat = Chat::where(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->first();

        if (!$chat) {
            // Чат не найден, создаем новый
            $chat = Chat::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $userId,
            ]);
        }

        return $chat;
    }
}
