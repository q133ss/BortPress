<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemController\StoreRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function categories()
    {
        return ItemCategory::withCount('items')->get();
    }

    public function items($id)
    {
        return Item::where('category_id', $id)->get();
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth('sanctum')->id();
        return Item::create($data);
    }
}
