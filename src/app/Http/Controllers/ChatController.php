<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function create(int $adv_id)
    {
        return (new ChatService())->create($adv_id);
    }
}
