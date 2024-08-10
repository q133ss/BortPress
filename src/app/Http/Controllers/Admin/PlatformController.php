<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileController\UpdateRequest;
use App\Models\Item;
use App\Models\PayFormat;
use App\Models\Payment;
use App\Models\Region;
use App\Models\Role;
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
        $users = User::where('role_id', function ($query){
            return $query->select('id')
                ->from('roles')
                ->where('slug','adv_platform')
                ->first();
        })
            ->withSort($request)
            ->get();

        $users->each(function ($user) {
            $user->subscribe_status = $user->subscribe_status();
            $user->activation_date = $user->activation_date();
            $user->purchase_amount = Payment::where('status', 'done')->pluck('sum')->sum();
        });

        return $users;
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
        $user = User::findOrFail($id);
        $data =
        [
            'company' => function($query) {
                $query->with('logo', 'documents', 'region');
            },
            'ads' => function ($query) use ($request) {
                $query->with('region');
                //$query->inventory = $query->item();
                //$query->type = $query->getType;
                $query->withFilter($request);
            },
            'archive' => function($query) {
                $query->with('region');
            },
            'role',
            'comments' => function($query) {
                $query->with('user');
            }
        ];

        if($user->role_id == Role::where('slug', 'advertiser')->pluck('id')->first())
        {
            unset($data['comments']);
        }

        return $user->load($data);
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
