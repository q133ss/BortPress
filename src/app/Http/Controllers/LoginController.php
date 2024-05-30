<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginController\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        return (new LoginService())->login($request->validated());
    }
}
