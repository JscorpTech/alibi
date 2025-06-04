<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Size;
use App\Services\Admin\SizeService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class SizeController extends Controller
{
    public SizeService $service;

    public function __construct()
    {
        $this->service = new SizeService();
    }

    public function index(): \Illuminate\Http\Response
    {
        return Response::view('admin.size.list', [
            'categories' => $this->service->index(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Response::view('admin.size.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NameRequest $request)
    {
        try {
            $this->service->create($request);

            return Redirect::route('size.index');
        } catch (\Throwable $e) {
            abort(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Size::findOrField($id);

        return Response::view('admin.size.edit', [
            'size' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NameRequest $request, string $id)
    {
        $category = Size::findOrField($id);
        $category->fill($request->only('name'));
        $category->save();

        return Redirect::route('size.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        Size::findOrField($id)->delete();

        return Redirect::back();
    }
}
