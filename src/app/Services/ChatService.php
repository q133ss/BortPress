<?php

namespace App\Services;

use App\Http\Requests\ChatController\SendRequest;
use App\Models\Ad;
use App\Models\Chat;
use App\Models\File;
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

        return Response()->json([
            'chat' => $chat->load(['receiver' => function ($query) {
                $query->select('id', 'name')
                    ->with(['company' => function ($query) {
                        $query->select('id', 'user_id', 'name');
                        $query->with('logo');
                    }]);
            }]),
            'ad' => [
                'name' => $ad->name,
                'is_offer' => $ad->is_offer
            ]
        ]);
    }

    public function messages(int $chat_id)
    {
        $chat = Chat::findOrFail($chat_id);
        if($chat->sender_id == Auth()->id() || $chat->receiver_id == Auth()->id()){
            return $chat->messages->load('file');
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

            unset($data['file']);

            $message = Message::create($data);

            if($request->hasFile('file')) {
                File::create([
                    'fileable_id' => $message->id,
                    'fileable_type' => 'App\Models\Message',
                    'category' => 'file',
                    'src' => env('APP_URL').$request->file('file')->store('messages', 'public')
                ]);
            }

            $pusher = new Pusher(
                '713314410e2c9ff64942',
                'a2943488eeda4502207e',
                '1591884',
                [
                    'cluster' => 'eu',
                    'useTLS' => true,
                ]
            );

            $pusher->trigger('chat-' . $chat_id, 'MessageSent', [
                'id' => $message->id,
                'text' => $message->text,
                'user' => Auth()->user(),
            ]);

            return $message->load('file');

        }else{
            return Response()->json(['message' => 'У вас нет прав для просмотра', 'errors' => ['error' => 'У вас нет прав для просмотра']], 403);
        }
    }
}
