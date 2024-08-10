<?php

namespace App\Services;

use App\Http\Requests\ChatController\SendRequest;
use App\Models\Ad;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Pusher\Pusher;

class ChatService
{
    public function create(int $adv_id)
    {
        $ad = Ad::findOrFail($adv_id);

        $userId = $ad->user_id;
        $currentUserId = Auth()->id();

        if(Auth('sanctum')->user()->role_id == Role::where('slug', 'advertiser')->pluck('id')->first()
            && User::find($userId)->role_id == Role::where('slug', 'advertiser')->pluck('id')->first())
        {
            abort(403, 'Вам не доступно создание чата');
        }

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

    public function messages(int $chat_id)
    {
        $chat = Chat::findOrFail($chat_id);
        if($chat->sender_id == Auth()->id() || $chat->receiver_id == Auth()->id()){
            return $chat->messages;
        }else{
            return Response()->json(['message' => 'У вас нет прав для просмотра', 'errors' => ['error' => 'У вас нет прав для просмотра']], 403);
        }
    }

    public function send(int $chat_id, SendRequest $request)
    {
        $chat = Chat::findOrFail($chat_id);
        if($chat->sender_id == Auth()->id() || $chat->receiver_id == Auth()->id()) {
            $data = $request->validated();
            $data['chat_id'] = $chat_id;
            $data['user_id'] = Auth()->id();

            $message = Message::create($data);

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );

            $pusher->trigger('chat-' . $chat_id, 'MessageSent', [
                'id' => $message->id,
                'text' => $message->text,
                'user' => Auth()->user(),
            ]);

            return $message;

        }else{
            return Response()->json(['message' => 'У вас нет прав для просмотра', 'errors' => ['error' => 'У вас нет прав для просмотра']], 403);
        }
    }
}
