<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Item;
use App\Models\PayFormat;
use App\Models\Region;
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
            $ad->item = Item::find($ad->item);
            $ad->user = User::find($ad->user_id);
            unset($ad->region_id);
            unset($ad->user_id);
        });
        return $ads;
    }

    public function show($id)
    {
        return Ad::findOrFail($id)->load('photo', 'document');
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
            $ad->item = Item::find($ad->item);
            $ad->user = User::find($ad->user_id);
            unset($ad->region_id);
            unset($ad->user_id);
        });

        return $ads;
    }

    public function showPhone($id)
    {
        $phone = Ad::findOrFail($id)
            ->owner?->phone;

        return Response()->json([
            'phone' => $phone
        ]);
    }
}
