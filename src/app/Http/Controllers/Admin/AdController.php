<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request, true);
    }
}
