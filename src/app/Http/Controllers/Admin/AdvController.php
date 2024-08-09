<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdvController extends Controller
{
    public function index(Request $request)
    {
        return User::where('role_id', function ($query){
            return $query->select('id')
                ->from('roles')
                ->where('slug','advertiser')
                ->first();
        })
            ->withSort($request)
            ->get();
    }
}
