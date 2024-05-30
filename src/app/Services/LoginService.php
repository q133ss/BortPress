<?php

namespace App\Services;

use App\Models\User;

class LoginService
{
    public function login(array $data): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $data['email'])->first();
        $token = $user->createToken('web');
        return Response()->json(['user' => $user, 'token' => $token->plainTextToken]);
    }
}
