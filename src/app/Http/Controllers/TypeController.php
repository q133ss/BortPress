<?php

namespace App\Http\Controllers;

use App\Services\TypeService;

class TypeController extends Controller
{
    public function index()
    {
        return (new TypeService())->index();
    }
}
