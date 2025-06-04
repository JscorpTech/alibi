<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Color;
use App\Services\Admin\ColorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class ColorController extends Controller
{
    public ColorService $service;

    public function __construct()
    {
        $this->service = new ColorService();
    }

    public function index(): \Illuminate\Http\Response
    {
        return Response::view('admin.color.list', [
            'categories' => $this->service->index(),
        ]);
    }

    public function create(): \Illuminate\Http\Response
    {
        return Response::view('admin.color.create');
    }

    public function store(NameRequest $request)
    {
        $this->service->create($request);

        return Redirect::route('color.index');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        $color = Color::findOrField($id);

        return Response::view('admin.color.edit', [
            'color' => $color,
        ]);
    }

    public function update(NameRequest $request, string $id)
    {
        $this->service->update($id, $request->only(['name', 'color']));

        return Redirect::route('color.index');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->service->delete($id);

        return Redirect::back();
    }
}
