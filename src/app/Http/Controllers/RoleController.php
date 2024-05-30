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
}
