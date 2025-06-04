<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\DashboardService;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    use BaseController;

    public DashboardService $service;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    public function index(Request $request): \Illuminate\Http\Response
    {
        $data = $this->service->index($request);

        return Response::view('admin.dashboard.main', $data);
    }
}
