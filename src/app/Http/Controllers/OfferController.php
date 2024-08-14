<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Item;
use App\Models\PayFormat;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //Предложения рекламных площадок
    public function index(Request $request)
    {
        $ads = Ad::leftJoin('users', 'users.id', 'ads.user_id')->where('users.is_block', 0)->where('is_offer', 1)->where('is_archive', 0)->withFilter($request)->with('photo')->select('ads.*')->get();

        return $ads;
    }

    public function show($id)
    {
        $ad =  Ad::findOrFail($id)->load('photo', 'document');

        return $ad;
    }

    public function unique(Request $request)
    {
        $ads = Ad::leftJoin('users', 'users.id', 'ads.user_id')
            ->where('users.is_block', 0)
            ->where('ads.is_unique', 1)
            ->where('ads.is_offer', 1)
            ->where('ads.is_archive', 0)
            ->withFilter($request)
            ->with('photo')
            ->select('ads.*')
            ->get();

        return $ads;
    }

    public function showPhone($id)
    {
        $owner = Ad::findOrFail($id)
            ->owner;

        $adv_role_id = Role::where('slug', 'advertiser')->pluck('id')->first();

        if(
            Auth('sanctum')->user()->role_id == $adv_role_id
            &&
            $owner->role_id == $adv_role_id
        )
        {
            abort(403, 'Вам не доступен просмотр номера');
        }

        return Response()->json([
            'phone' => $owner->phone,
            'name' => $owner->name
        ]);
    }
}
