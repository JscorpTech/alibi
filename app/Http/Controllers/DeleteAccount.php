<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DeleteAccount extends Controller
{
    use BaseController;

    function index(): JsonResponse
    {
        return Response::json(['success' => true]);
    }
}

