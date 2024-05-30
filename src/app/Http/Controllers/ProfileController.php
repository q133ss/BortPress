<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileController\UpdateRequest;
use App\Http\Resources\ProfileController\IndexResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(): IndexResource
    {
        return new IndexResource(Auth()->user());
    }

    public function update(UpdateRequest $request)
    {
        return (new ProfileService())->update($request);
    }
}
