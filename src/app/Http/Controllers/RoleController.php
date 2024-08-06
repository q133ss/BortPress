<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function roles()
    {
        return Role::whereIn('slug', ['advertiser','adv_platform'])->get();
    }

    public function admin()
    {
        return Role::where('slug', 'admin')->first();
    }

    public function all()
    {
        return Role::get();
    }
}
