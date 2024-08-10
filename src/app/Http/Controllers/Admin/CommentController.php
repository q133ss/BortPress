<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommentController\StoreRequest;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreRequest $request, $id)
    {
        return Comment::create([
            'comment' => $request->comment,
            'commentable_type' => 'App\Models\User',
            'commentable_id' => $id,
            'user_id' => Auth('sanctum')->id()
        ]);
    }

    public function getById($id)
    {
        return User::findOrFail($id)
            ->comments;
    }
}
