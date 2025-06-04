<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Services\LocaleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function index(): Collection|array
    {
        return Category::query()->orderByDesc('id')->get();
    }

    public function create($request): void
    {
        $image = Storage::putFile('categories', $request->file('image'));
        Category::query()->create(
            [
                ...$request->only([
                    ...LocaleService::getLocaleFields('name'),
                ]),
                'position' => $request->input('position'),
                'gender'   => $request->input('gender'),
                'image'    => $image,
            ]
        );
    }
}
