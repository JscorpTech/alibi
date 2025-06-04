<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class LoginController extends Controller
{
    use BaseController;

    public AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function index(): \Illuminate\Http\Response
    {
        return Response::view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $this->service->login($request->get('phone'), $request->get('password'), $request->get('remember'));

            return Redirect::route('dashboard');
        } catch (\Throwable $e) {
            return Redirect::back()->withErrors(['phone' => $e->getMessage()]);
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return Redirect::route('home');
    }
}
