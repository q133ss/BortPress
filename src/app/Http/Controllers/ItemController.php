<?php

namespace App\Http\Controllers;

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
}
