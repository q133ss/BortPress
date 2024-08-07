<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //Предложения рекламных площадок
    public function index(Request $request)
    {
        return Ad::leftJoin('users', 'users.id', 'ads.user_id')->where('users.is_block', 0)->where('is_offer', 1)->where('is_archive', 0)->withFilter($request)->with('photo')->select('ads.*')->get();
    }

    public function show($id)
    {
        return Ad::findOrFail($id)->load('photo', 'document');
    }

    public function unique(Request $request)
    {
        return Ad::leftJoin('users', 'users.id', 'ads.user_id')
            ->where('users.is_block', 0)
            ->where('ads.is_unique', 1)
            ->where('ads.is_offer', 1)
            ->where('ads.is_archive', 0)
            ->withFilter($request)
            ->with('photo')
            ->select('ads.*')
            ->get();
    }
}
