<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Models\Ad;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function create(CreateRequest $request)
    {
        return (new OfferService())->create($request);
    }

    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request);
    }
}
