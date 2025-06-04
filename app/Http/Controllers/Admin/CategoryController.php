<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryCreateReuqest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use App\Services\LocaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public CategoryService $service;

    public function __construct()
    {
        $this->service = new CategoryService();
    }

    public function index(): \Illuminate\Http\Response
    {
        return Response::view('admin.category.list', [
            'categories' => $this->service->index(),
        ]);
    }

    public function create(): \Illuminate\Http\Response
    {
        return Response::view('admin.category.create');
    }

    public function store(CategoryCreateReuqest $request)
    {
        try {
            $this->service->create($request);

            return Redirect::route('category.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            abort(500);
        }
    }

    public function show(string $id): void
    {
        abort(404);
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function edit(string $id): \Illuminate\Http\Response
    {
        $category = Category::findOrField($id);

        return Response::view('admin.category.edit', [
            'category' => $category,
        ]);
    }

    /**
     * @param CategoryUpdateRequest $request
     * @param string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(CategoryUpdateRequest $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $category = Category::findOrField($id);

        $image = $category->image;
        if ($request->hasFile('image')) {
            if ($image != null) {
                Storage::delete($image);
            }
            $image = Storage::putFile('categories', $request->file('image'));
        }

        $category->fill([
            ...$request->only(LocaleService::getLocaleFields('name')),
            'position' => $request->input('position'),
            'gender'   => $request->input('gender'),
            'image'    => $image,
        ]);
        $category->save();

        return Redirect::route('category.index');
    }

    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $category = Category::findOrField($id);
        if ($category->image != null) {
            Storage::delete($category->image);
        }
        $category->delete();

        return Redirect::back();
    }
}
