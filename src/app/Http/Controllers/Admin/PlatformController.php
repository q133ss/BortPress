<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileController\UpdateRequest;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Добавляем сортировку
        return User::where('role_id', function ($query){
            return $query->select('id')
                ->from('roles')
                ->where('slug','adv_platform')
                ->first();
        })
            ->withSort($request)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return User::with(['company','ads' => function ($query) use ($request) {
            $query->withFilter($request);
        },'archive'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        return (new ProfileService())->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->update(['is_block' => 1]);
        return Response()->json(['message' => 'true']);
    }
}
