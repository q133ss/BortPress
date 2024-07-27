<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Models\Ad;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        return Ad::where('user_id', Auth('sanctum')->id())->where('is_offer', 1)->where('is_archive', 0)->withFilter($request)->with('photo')->get();
    }
    public function create(CreateRequest $request)
    {
        return (new OfferService())->create($request);
    }

    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request);
    }

    public function delete(int $id)
    {
        return (new OfferService())->delete($id);
    }
}
