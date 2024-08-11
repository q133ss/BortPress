<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return (new NotificationService())->index();
    }

    public function clear()
    {
        return (new NotificationService())->clear();
    }

    public function clearCategory($id)
    {
        return (new NotificationService())->clearCategory($id);
    }
}
