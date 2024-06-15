<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function uniques()
    {
        return Ad::where('is_unique', true)->get();
    }
}
