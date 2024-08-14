<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileController\LogoUpdateRequest;
use App\Http\Requests\ProfileController\UpdateRequest;
use App\Http\Resources\ProfileController\IndexResource;
use App\Models\Ad;
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

    public function archive(Request $request)
    {
        return Ad::where('user_id', Auth()->id())->where('is_archive', 1)->with('region')->withFilter($request)->get();
    }

    public function logoUpdate(LogoUpdateRequest $request)
    {
        return (new ProfileService())->logoUpdate($request);
    }
}
