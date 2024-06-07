<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\OfferController\CreateRequest;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function create(CreateRequest $request)
    {
        return (new OfferService())->create($request, false);
    }

    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request);
    }
}
