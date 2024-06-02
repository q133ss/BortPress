<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayFormatController extends Controller
{
    public function index()
    {
        return DB::table('pay_formats')->get();
    }
}
