<?php

namespace App\Services;

use App\Models\User;

class LoginService
{
    public function login(array $data): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $data['email'])->first();
        $token = $user->createToken('web');

        $changedUser = $user->load('role', 'company');
        $changedUser->company->setRelation('formats', $changedUser->company->formats());
        $changedUser['subscribe_status'] = $user->subscribe_status();
        return Response()->json(['user' => $changedUser, 'token' => $token->plainTextToken]);
    }
}
