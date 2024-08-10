<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Item;
use App\Models\PayFormat;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function uniques()
    {
        $ads = Ad::where('is_unique', true)->get();
        return $ads;
    }

    public function catalog($type)
    {
        $is_offer = 0;
        if($type == 'offers'){
            $is_offer = 1;
        }

        $ads = Ad::leftJoin('users', 'users.id', 'ads.user_id')->where('users.is_block', 0)->where('is_offer', $is_offer)->where('is_archive', 0)->orderBy('ads.created_at', 'DESC')->with('photo')->limit(10)->select('ads.*')->get();

        return $ads;
    }
}
