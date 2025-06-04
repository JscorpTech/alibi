<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

class GuestController extends Controller
{
    use BaseController;

    public function get_me(Request $request): JsonResponse
    {
        $service = new AuthService();
        $response = $service->guest($request);
        return $this->success(data: $response);
    }

    function register(Request $request): JsonResponse
    {
        $request->validate([
            "name" => "required|string",
        ]);
        $service = new AuthService();
        $guest = $service->register_guest($request->input("name"));
        return $this->success(data: ["token" => $guest->token]);
    }
}
