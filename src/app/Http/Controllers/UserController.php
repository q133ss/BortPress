<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileController\IndexResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        return new IndexResource($user);
    }
}
