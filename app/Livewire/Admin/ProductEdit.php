<?php

namespace App\Livewire\Admin;

use App\Http\Helpers\Helper;
use App\Http\Requests\Admin\Product\CreateRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    public mixed $sizes;
    public array $product_colors = [];
    public mixed $categories;
    public array $images = [];
    public $image;
    public mixed $colors;
    public mixed $product;
    public mixed $counts;

    public mixed $old_colors;
    public mixed $old_c;
    public mixed $old_sc;
    public mixed $sizeImages;

    protected $listeners = ['deleteImage' => 'removeImage', 'setColor' => 'setColor']; // Listeners

    /**
     * Set product color for edit page
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

    /**
     * @throws ValidationException
     */
    public function submit($data, $product, $model, $sizes)
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

        if ($is_error) {
            return;
        }

        $data = [
            'name_ru'       => $data['name_ru'],
            'desc_ru'       => $data['desc_ru'],
            'gender'        => $data['gender'],
            'offers'        => isset($model['offers']) ? json_encode($model['offers']) : json_encode([]),
            'price'         => Helper::clearSpace($data['price']),
            'count'         => json_encode([]),
            'discount'      => $data['discount'] == '' ? 0.0 : (float) Helper::clearSpace($data['discount']),
            'status'        => $data['status'],
            'size_infos_id' => $data['size-image'],
        ];

        if ($this->image != null) {
            $image = Storage::putFile('products/', $this->image);
            $data['image'] = $image;
        }

        $product = Product::query()->where(['id' => $product]);
        $product->update($data);
        $product = $product->first();

        $product->sizes()->detach();
        foreach ($sizes as $size) {
            $product->sizes()->attach(Size::query()->where(['id' => $size])->first()->id);
        }
        $product->categories()->detach();
        foreach ($model['category'] as $item) {
            $product->categories()->attach($item);
        }
        $product->subcategories()->detach();
        foreach ($model['subcategory'] as $item) {
            $product->subcategories()->attach($item);
        }

        return $this->redirect(route('product.show', $product->id));
    }

    public function removeImage($id): void
    {
        unset($this->images[$id]);
    }

    /**
     * @throws Exception
     */
    public function mount($product): void
    {
        $this->sizeImages = \App\Models\SizeInfo::query()->orderByDesc('id')->get();
        $this->product = Product::findOrField($product);
        $this->sizes = Size::query()->get();
        $this->colors = Color::query()->get();
        $this->categories = Category::query()->get();

        $this->old_c = array_column($this->product->categories()->get()->toArray(), 'id');
        $this->old_sc = array_column($this->product->subcategories()->get()->toArray(), 'id');

        $ss = [];

        foreach ($this->product->colors as $key => $item) {
            $this->old_colors[] = [
                'name'  => $item->color->name,
                'color' => $item->color->id,
            ];
        }

        $this->counts = $ss;
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.product-edit');
    }
}
