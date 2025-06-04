<?php

namespace App\Livewire\Admin;

use App\Http\Helpers\Helper;
use App\Http\Requests\Admin\Product\CreateRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductColors;
use App\Models\Size;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{
    use WithFileUploads;

    public mixed $sizes;
    public array $product_colors = [];
    public mixed $categories;
    public array $images = [];
    public $image;
    public mixed $colors;
    public mixed $sizeImages;

    protected $listeners = ['deleteImage' => 'removeImage', 'setColor' => 'setColor']; // Listeners

    /**
     * Set product color
     *
     * @param $image
     * @param $color
     * @return void
     */
    public function setColor($image, $color): void
    {
        if ($color == 'none') {
            unset($this->product_colors[$image]);

            return;
        }

        $color = Color::query()->where(['id' => $color])->get()->first();
        $this->product_colors[$image] = $color;
    }

    public function submit($data, $model, $sizes)
    {
        $validator = Validator::make($data, (new CreateRequest())->rules());

        $is_error = false;

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $this->addError($key, $value[0]);
            }
            $is_error = true;
        } else {
            foreach ($validator->validated() as $key => $item) {
                $this->resetErrorBag($key);
            }
        }

        if (count($this->images) == 0) {
            $this->addError('images', __('images:required'));
            $is_error = true;
        } else {
            $this->resetErrorBag('images');
        }

        if ($this->image == null) {
            $this->addError('image', __('image:required'));
            $is_error = true;
        } else {
            $this->resetErrorBag('image');
        }

        if (count($this->product_colors) == 0) {
            $this->addError('product_colors', __('product:colors:required'));
            $is_error = true;
        } else {
            $this->resetErrorBag('product_colors');
        }

        if ($is_error) {
            return;
        }

        $image = Storage::putFile('products/', $this->image);
        $product = Product::query()->create([
            'name_ru'  => $data['name_ru'],
            'desc_ru'  => $data['desc_ru'],
            'offers'   => isset($model['offers']) ? json_encode($model['offers']) : json_encode([]),
            'image'    => $image,
            'gender'   => $data['gender'],
            'price'    => Helper::clearSpace($data['price']),
            'discount' => $data['discount'] == '' ? 0.0 : (float) Helper::clearSpace($data['discount']),
            'count'    => json_encode([]),
            'status'   => $data['status'],
        ]);
        $product->size_infos_id = $data['size-image'];
        $product->save();

        foreach ($model['category'] as $c) {
            $product->categories()->attach($c);
        }
        foreach ($model['subcategory'] as $c) {
            $product->subcategories()->attach($c);
        }

        foreach ($this->images as $image) {
            $file = new Media(['path' => Storage::putFile('products/', $image)]);
            $product->images()->save($file);
        }

        foreach ($this->product_colors as $key => $product_color) {
            if (!isset($this->images[$key])) {
                continue;
            }

            $file = new Media(['path' => Storage::putFile('products/', $this->images[$key])]);

            if (!isset($product_color->id)) {
                continue;
            }

            $product_color = ProductColors::query()->create([
                'product_id' => $product->id,
                'color_id'   => $product_color->id,
            ]);

            $product_color->image()->save($file);
        }

        foreach ($sizes as $size) {
            $product->sizes()->attach(Size::query()->where(['id' => $size])->first()->id);
        }

        return $this->redirect(route('product.show', $product->id));
    }

    public function removeImage($id): void
    {
        unset($this->images[$id]);
    }

    public function mount()
    {
        $this->sizeImages = \App\Models\SizeInfo::query()->orderByDesc('id')->get();
        $this->sizes = Size::query()->get();
        $this->colors = Color::query()->get();
        $this->categories = Category::query()->get();
    }

    public function render()
    {
        return view('livewire.admin.product-create');
    }
}
