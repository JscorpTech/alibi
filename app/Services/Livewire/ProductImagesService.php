<?php

namespace App\Services\Livewire;

use App\Models\Color;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductColors;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ProductImagesService
{
    public Product|null $product;

    public function __construct($product = null)
    {
        $this->product = $product;
    }

    /**
     * Get product images
     *
     * @return mixed
     */
    public function getImages(): mixed
    {
        return $this->product->images->toArray();
    }

    /**
     * Get product colors
     *
     * @return array|object|null
     */
    public function getColors(): array|null|object
    {
        return $this->product->colors;
    }

    /**
     * the image relation color check
     *
     * @param $colors
     * @param string $path
     * @return mixed
     */
    public function filter($colors, string $path): mixed
    {
        return $colors->filter(function ($data) use ($path) {
            return $data->image?->path == $path;
        });
    }

    /**
     * Save new images
     *
     * @param $images
     * @return bool
     */
    public function saveImages($images): bool
    {
        try {
            foreach ($images as $index => $image) {
                $path = ($image['status'] ?? 'old') == 'new' ? Storage::putFile('products/', $image['path']) : $image['path'];
                $images[$index]['path'] = $path;

                $media = $this->product->images()->firstOrCreate(
                    ['path' => $image['path']],
                    ['path' => $path]
                );

                if (isset($image['remove_color'])) {
                    $res = $this->filter($this->getColors(), $media->path);
                    foreach ($res as $re) {
                        ProductColors::query()->find($re->id)->delete();
                    }
                }

                if (isset($image['color']) and !isset($image['remove_color'])) {
                    if (!($this->filter($this->getColors(), $media->path)->count() >= 1)) {
                        $color = ProductColors::query()->firstOrCreate([
                            'color_id'   => $image['color']['id'],
                            'product_id' => $this->product->id,
                        ]);
                        $image = new Media(['path' => $path]);
                        $color->image()->save($image);
                    } else {
                        $this->product->colors()->whereHas('image', function ($query) use ($path) {
                            $query->where('path', $path);
                        })->update([
                            'color_id' => $image['color']['id'],
                        ]);
                    }
                }
            }
            $this->product->images()->whereNotIn('path', array_column($images, 'path'))->delete();

            return true;
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getLine());

            return false;
        }
    }

    /**
     * Get all colors
     *
     * @return Collection|array
     */
    public function allColors(): Collection|array
    {
        return Color::query()->get();
    }
}
