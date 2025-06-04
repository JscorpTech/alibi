<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditStatusRequest;
use App\Http\Requests\Filters\OrdersFilter;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\ProductOption;
use App\Services\Admin\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public OrderService $service;

    public function __construct()
    {
        $this->service = new OrderService();
    }

    public function index(OrdersFilter $filter): \Illuminate\Http\Response
    {
        $data = $this->service->filter($filter);

        return Response::view('admin.order.list', $data);
    }

    public function create(): void
    {
        abort(404);
    }

    public function store(Request $request): void
    {
        abort(404);
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function show(string $id): \Illuminate\Http\Response
    {
        $order = OrderGroup::findOrField($id);

        return Response::view('admin.order.detail', [
            'order' => $order,
        ]);
    }

    public function edit(string $id): void
    {
        abort(404);
    }

    public function update(Request $request, string $id): void
    {
        abort(404);
    }

    public function destroy(Request $request, string $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $this->service->delete($id);
        $route = $request->input('route');

        return $route != null ? redirect($route) : redirect()->back();
    }

    /**
     * Edit order status
     * @throws Exception
     */
    public function editStatus(EditStatusRequest $request, $id): RedirectResponse
    {
        $status = $request->input('status');
        $order = OrderGroup::findOrField($id);

        $order->status = $status;

        $order->save();

        return redirect()->back();
    }
}
