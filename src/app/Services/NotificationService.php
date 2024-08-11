<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function index()
    {
//        return Auth('sanctum')->user()->notifications->groupBy(function ($item){
//            return $item->category->name ?? '---';
//        });

        $groupedNotifications = Auth('sanctum')->user()->notifications->groupBy(function ($item) {
            return $item->category->name ?? '---';
        })->map(function ($items, $category) {
            return [
                'last_notifications_date' => $items->max('created_at'),
                'notifications' => $items,
            ];
        });

        return $groupedNotifications;
    }
    public function create(string $title, string $text, array $user_id, $category_id, $link = null): true
    {
        foreach ($user_id as $id) {
            Notification::create([
                'title'   => $title,
                'text'    => $text,
                'link'    => $link,
                'user_id' => $id,
                'category_id' => $category_id
            ]);
        }

        return true;
    }

    public function clear()
    {
        Notification::where('user_id', Auth('sanctum')->id())
            ->delete();

        return Response()->json([
            'message' => 'true'
        ]);
    }

    public function clearCategory($id)
    {
        Notification::where('user_id', Auth('sanctum')->id())
            ->where('category_id', $id)
            ->delete();

        return Response()->json([
            'message' => 'true'
        ]);
    }
}
