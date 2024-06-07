<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        return Ad::where('is_offer', 0)->where('is_archive', 0)->withFilter($request)->with('photo')->get();
    }
}
