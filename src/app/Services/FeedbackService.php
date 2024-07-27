<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function store(array $data)
    {
        return Feedback::create($data);
    }
}
