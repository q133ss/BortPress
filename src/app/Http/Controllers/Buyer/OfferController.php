<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\OfferController\CreateRequest;
use App\Models\Ad;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        return Ad::withFilter($request)->where('user_id', Auth('sanctum')->id())
            ->where('is_offer', false)
            ->where('is_archive', false)
            ->get();
    }

    public function archive(Request $request)
    {
        return Ad::withFilter($request)->where('user_id', Auth('sanctum')->id())
            ->where('is_offer', false)
            ->where('is_archive', true)
            ->with('region')
            ->get();
    }
    public function create(CreateRequest $request)
    {
        return (new OfferService())->create($request, false);
    }

    public function show($id)
    {
        $ad = Ad::findOrFail($id)->load('photo', 'document');

        return Response()->json([
            'ad' => $ad->item(),
            'items' => $ad->item()
        ]);
    }

    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request);
    }

    public function delete($id)
    {
        return (new OfferService())->delete($id);
    }
}
