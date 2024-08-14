<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id)->load('company', 'ads');

        return Response()->json([
            'user' => $user,
            'ads' => $user->load('ads'),
            'company' => $user->company?->load('documents', 'logo')
        ]);
    }
}
