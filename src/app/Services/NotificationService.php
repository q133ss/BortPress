<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function index()
    {
        $groupedNotifications = Auth('sanctum')->user()->notifications->groupBy(function ($item) {
            return $item->category_id ?? '---';
        })->mapWithKeys(function ($items, $categoryId) {
            $categoryName = $items->first()->category->name ?? '---';

            return [
                $categoryName => [
                    'last_notifications_date' => $items->max('created_at'),
                    'category_id' => $categoryId,
                    'notifications' => $items,
                ],
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
