<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatController\SendRequest;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return Auth()->user()->chats();
    }
    public function create(int $adv_id)
    {
        return (new ChatService())->create($adv_id);
    }

    public function messages(int $chat_id)
    {
        return (new ChatService())->messages($chat_id);
    }

    public function send(int $chat_id, SendRequest $request)
    {
        return (new ChatService())->send($chat_id, $request);
    }
}
