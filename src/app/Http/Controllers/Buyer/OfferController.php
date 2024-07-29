<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\OfferController\CreateRequest;
use App\Models\Ad;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        return Ad::where('user_id', Auth('sanctum')->id())
            ->where('is_offer', false)
            ->where('is_archive', false)
            ->get();
    }

    public function archive()
    {
        return Ad::where('user_id', Auth('sanctum')->id())
            ->where('is_offer', false)
            ->where('is_archive', true)
            ->get();
    }
    public function create(CreateRequest $request)
    {
        return (new OfferService())->create($request, false);
    }

    public function show($id)
    {
        return Ad::findOrFail($id)->load('photo', 'document', 'item');
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
