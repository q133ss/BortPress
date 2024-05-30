<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class RegisterService
{
    public function register(array $data): \Illuminate\Http\JsonResponse
    {
        $user = User::create($data);
        $token = $user->createToken('web');
        return Response()->json(['user' => $user, 'token' => $token->plainTextToken]);
    }
}
