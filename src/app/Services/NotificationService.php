<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function index()
    {
        return Auth('sanctum')->user()->notifications;
    }
    public function create(string $title, string $text, array $user_id, $link = null): true
    {
        foreach ($user_id as $id) {
            Notification::create([
                'title'   => $title,
                'text'    => $text,
                'link'    => $link,
                'user_id' => $id
            ]);
        }

        return true;
    }
}
