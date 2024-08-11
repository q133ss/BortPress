<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class AdvController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role_id', function ($query){
            return $query->select('id')
                ->from('roles')
                ->where('slug','advertiser')
                ->first();
        })
            ->withSort($request)
            ->get();

        $users->each(function ($user) {
            $user->subscribe_status = $user->subscribe_status();
            $user->activation_date = $user->activation_date();
            $user->purchase_amount = Payment::where('status', 'done')->pluck('sum')->sum();
            $user->load('company');
        });

        return $users;
    }
}
