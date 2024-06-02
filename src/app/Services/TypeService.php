<?php

namespace App\Services;

use App\Models\Type;
use Illuminate\Support\Facades\Cache;

class TypeService
{
    public function index()
    {
        $result = Cache::remember('types', 60*24*30, function () {
            $types = Type::with('children')->whereNull('parent_id')->get();
            return $types->map(function ($parent) {
                return [
                    'id'     => $parent->id,
                    'name'   => $parent->name,
                    'childs' => $parent->children->map(function ($child) use ($parent) {
                        return [
                            'id'        => $child->id,
                            'name'      => $child->name,
                            'parent_id' => $parent->id
                        ];
                    })->all()
                ];
            })->all();
        });

        return Response()->json([
            'data' => $result
        ]);
    }
}
