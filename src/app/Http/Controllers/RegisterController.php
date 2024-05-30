<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterController\RegisterRequest;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return (new RegisterService())->register($request->validated());
    }
}
