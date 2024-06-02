<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //Предложения рекламных площадок
    public function adv(Request $request)
    {
        return Ad::withFilter($request)->with('photo')->get();
    }
}
