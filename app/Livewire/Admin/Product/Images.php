<?php

namespace App\Livewire\Admin\Product;

use App\Models\Color;
use App\Models\Product;
use App\Services\Livewire\ProductImagesService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;
use Livewire\WithFileUploads;

class Images extends Component
{
    use WithFileUploads;

    public mixed $images;
    public int $id;
    public array|null|object $colors;
    public ?array $image;
    public mixed $product;
    public array|Collection $allColors;

    protected $listeners = ['uploaded', 'delete', 'setColor'];

    /**
     * Set image color
     *
     * @param $index
     * @param $color
     * @return void
     */
    #[NoReturn]
    public function setColor($index, $color): void
    {
        try {
            if ($color == 'none') {
                $this->images[$index]['remove_color'] = true;
                $this->images[$index]['color'] = [
                    'name' => '',
                ];
                $this->dispatch('alert', ['message' => 'Rang olib tashlandi']);

                return;
            }

            $color = Color::query()->find($color);
            $this->images[$index]['color'] = [
                'name' => $color->name,
                'id'   => $color->id,
            ];
            $this->dispatch('alert', ['message' => "Rang o'zgartirildi"]);
        } catch (\Throwable $e) {
            $this->dispatch('alert', ['message' => "Rang O'zgartirishda xatolik", 'type' => 'danger']);
            Log::error($e->getMessage());
        }
    }

    /**
     * Image uploaded emit
     *
     * @return void
     */
    #[NoReturn]
    public function uploaded(): void
    {
        foreach ($this->image as $item) {
            $this->images[] = [
                'path'   => $item,
                'status' => 'new',
            ];
        }
        $this->image = null;
        $this->dispatch('alert', ['message' => __('Rasm yuklandi')]);
    }

    /**
     * Delete image from images list
     *
     * @param $index
     * @return void
     */
    public function delete($index): void
    {
        unset($this->images[$index]);
    }

    /**
     * Update images
     *
     * @return void
     */
    #[NoReturn]
    public function submit(): void
    {
        $service = new ProductImagesService($this->product);
        $service->saveImages($this->images);
        $this->dispatch('alert', ['message' => __('Yangilanishlar saqlandi')]);
    }

    #[NoReturn]
    public function mount(): void
    {
        $this->product = Product::query()->where(['id' => $this->id])->first();
        $service = new ProductImagesService($this->product);

        $this->colors = $service->getColors();
        $this->images = $service->getImages();
        $this->allColors = $service->allColors();
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.product.images')->layout('components.layouts.main');
    }
}
