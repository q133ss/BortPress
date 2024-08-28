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

        $changedUser = $user->load('role', 'company');
        $changedUser->company->setRelation('formats', $changedUser->company->formats());
        $changedUser['subscribe_status'] = $user->subscribe_status();

        return Response()->json(['user' => $changedUser, 'token' => $token->plainTextToken]);
    }
}
