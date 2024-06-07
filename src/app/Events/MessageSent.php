<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn()
    {
        return new Channel('chat-'.$this->message->chat_id);
    }

    public function broadcastWith()
    {
        Log::info('Сообщение отправлено:', [
            'id' => $this->message->id,
            'text' => $this->message->text,
            'user' => $this->user
        ]);
        return [
            'id' => $this->message->id,
            'text' => $this->message->text,
            'user' => $this->user
        ];
    }
}
