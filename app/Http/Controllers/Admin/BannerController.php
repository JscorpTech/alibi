<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BannerController extends Controller
{
    use BaseController;

    public function index(): \Illuminate\Http\Response
    {
        $banners = Banner::query()->orderByDesc('id')->get();

        return Response::view('admin.banner.list', [
            'banners' => $banners,
        ]);
    }
}
