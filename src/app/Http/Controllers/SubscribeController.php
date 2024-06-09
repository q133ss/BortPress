<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
{
    public function check()
    {
        $user = Auth()->user();
        if($user->subscribe_end == null){
            return Response()->json([
                'status' => 'false',
                'message' => 'Подписка не оплачена'
            ], 403);
        }

        if(now()->greaterThan(Carbon::parse($user->subscribe_end))){
                return Response()->json([
                    'status' => 'false',
                    'message' => 'Подписка не продлена'
                ], 403);
        }

        if(now()->lessThan(Carbon::parse($user->subscribe_end))){
            return Response()->json([
                'status' => 'true',
                'message' => 'Подписка активна'
            ], 200);
        }

        Log::error('Ошибка проверки подписки. UserID:'.$user->id);
        return Response()->json([
            'status' => 'false',
            'message' => 'Ошибка'
        ], 403);
    }
}
