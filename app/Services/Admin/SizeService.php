<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Services\LocaleService;
use Illuminate\Database\Eloquent\Collection;

class SizeService
{
    public function index(): Collection|array
    {
        return Size::query()->orderByDesc('id')->get();
    }

    public function create($request): void
    {
        Size::query()->create(
            $request->only([
                ...LocaleService::getLocaleFields('name'),
            ])
        );
    }
}
