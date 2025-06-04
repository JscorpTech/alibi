<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\HomeService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    use BaseController;

    public HomeService $service;

    public function __construct()
    {
        $this->service = new HomeService();
    }

    /**
     * Get products and banners
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        $data = $this->service->index();

        return Response::view('user.home', $data);
    }

    /**
     * @throws Exception
     */
    public function show($id): \Illuminate\Http\Response
    {
        $data = $this->service->show($id);

        return Response::view('user.show', $data);
    }

    /**
     * Categories page
     *
     * @param string $type
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function category(string $type = 'category', $id = null): \Illuminate\Http\Response
    {
        $categories = $this->service->categories();

        return Response::view('user.list', [
            'categories' => $categories,
            'type'       => $type,
            'id'         => $id,
        ]);
    }

    /**
     * User cabinet
     *
     * @return \Illuminate\Http\Response
     */
    public function cabinet(): \Illuminate\Http\Response
    {
        $orders = Auth::user()->OrderGroup()->orderByDesc('id')->get();
        $basket = Auth::user()->likes()->orderByDesc('id')->get();

        return Response::view('user.cabinet', [
            'orders' => $orders,
            'basket' => $basket,
            'user'   => Auth::user(),
        ]);
    }
}
