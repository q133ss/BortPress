<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackController\StoreRequest;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(StoreRequest $request)
    {
        return (new FeedbackService())->store($request->validated());
    }
}
