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
        $ads->each(function ($ad) {
            $ad->pay_format = PayFormat::whereIn('id', $ad->pay_format)->get();
            $ad->region = Region::find($ad->region_id);
            $ad->type = $ad->getType;
            //$ad->user = User::find($ad->user_id);
            $ad->inventory = Item::whereIn('id', $ad->inventory)->get();
            unset($ad->region_id);
            unset($ad->getType);
            //unset($ad->user_id);
        });
        return $ads;
    }

    public function show($id)
    {
        $ad =  Ad::findOrFail($id)->load('photo', 'document');
        $ad->pay_format = PayFormat::whereIn('id', $ad->pay_format)->get();
        $ad->region = Region::find($ad->region_id);
        $ad->type = $ad->getType;
        $ad->inventory = Item::whereIn('id', $ad->inventory)->get();
        unset($ad->region_id);
        unset($ad->getType);
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

        $ads->each(function ($ad) {
            $ad->pay_format = PayFormat::whereIn('id', $ad->pay_format)->get();
            $ad->region = Region::find($ad->region_id);
            $ad->type = $ad->getType;
            //$ad->user = User::find($ad->user_id);
            $ad->inventory = Item::whereIn('id', $ad->inventory)->get();
            unset($ad->region_id);
            unset($ad->getType);
            //unset($ad->user_id);
        });

        return $ads;
    }

    public function showPhone($id)
    {
        $owner = Ad::findOrFail($id)
            ->owner;

        if(Auth('sanctum')->user()->role_id == Role::where('slug', 'advertiser')->pluck('id')->first())
        {
            abort(403, 'Вам не доступен просмотр номера');
        }

        return Response()->json([
            'phone' => $owner->phone,
            'name' => $owner->name
        ]);
    }
}
