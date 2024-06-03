<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //Предложения рекламных площадок
    public function index(Request $request)
    {
        return Ad::where('is_offer', true)->withFilter($request)->with('photo')->get();
    }

    public function show($id)
    {
        return Ad::findOrFail($id)->load('photo', 'document');
    }
}
