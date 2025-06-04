<?php

namespace App\Services\Admin;

use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ColorService
{
    public function index(): Collection|array
    {
        return Color::query()->orderByDesc('id')->get();
    }

    public function create($request): void
    {
        Color::query()->create(
            $request->only([
                'color',
                'name',
            ])
        );
    }

    public function update($id, $data): bool
    {
        try {
            $category = Color::findOrField($id);
            $category->fill($data);
            $category->save();

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            Color::findOrField($id)->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
