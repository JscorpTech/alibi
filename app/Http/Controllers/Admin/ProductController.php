<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\CreateRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Services\Admin\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    public function index(Request $request): \Illuminate\Http\Response
    {
        return Response::view('admin.product.list', $this->service->index($request));
    }

    public function create(): \Illuminate\Http\Response
    {
        $categories = CategoryResource::collection(Category::query()->get());
        $sizes = Size::query()->get();
        $colors = Color::query()->get();

        return Response::view('admin.product.create', [
            'categories' => $categories,
            'sizes'      => $sizes,
            'colors'     => $colors,
        ]);
    }

    public function store(CreateRequest $request): RedirectResponse
    {
        $product = $this->service->store($request);

        return Redirect::route('product.show', $product->id);
    }

    public function show(string $id): \Illuminate\Http\Response
    {
        return Response::view('admin.product.detail', $this->service->show($id));
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function edit(string $id): \Illuminate\Http\Response
    {
        $product = Product::findOrField($id);
        $categories = CategoryResource::collection(Category::query()->get());

        return Response::view('admin.product.edit', [
            'product'    => $product,
            'categories' => $categories,
            'sizes'      => Size::query()->get(),
            'colors'     => Color::query()->get(),
        ]);
    }

    public function update(UpdateRequest $request, string $id): RedirectResponse
    {
        return Redirect::route('product.show', $this->service->update($id, $request)->id);
    }

    /**
     * @param string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(string $id): RedirectResponse
    {
        Product::findOrField($id)->delete();

        return Redirect::route('product.index');
    }
}
