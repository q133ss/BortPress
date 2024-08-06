<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        return Ad::leftJoin('users', 'users.id', 'ads.user_id')->where('users.is_block', 0)->where('is_offer', 0)->where('is_archive', 0)->withFilter($request)->with('photo')->select('ads.*')->get();
    }
}
