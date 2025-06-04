<?php

namespace App\Livewire\Admin\Product;

use App\Http\Helpers\Helper;
use App\Models\Product;
use App\Models\ProductOption;
use Livewire\Component;

class Options extends Component
{
    public $product;
    public $counts;
    public $options;

    public function mount($id): void
    {
        $this->product = Product::findOrField($id);
        $this->options = $this->product->options;
    }

    public function submit($data)
    {
        foreach ($data as $key => $value) {
            preg_match("/count\[(.*?)\-(.*?)\]*$/", $key, $match);
            $color = $match[2];
            $size = $match[1];

            $check = ProductOption::query()->where([
                'product_id' => $this->product->id,
                'size_id'    => $size,
                'color_id'   => $color,
            ]);

            if ($check->exists()) {
                $check->update(['count' => Helper::clearSpace($value)]);
                continue;
            }

            ProductOption::query()->create([
                'product_id' => $this->product->id,
                'size_id'    => $size,
                'color_id'   => $color,
                'count'      => Helper::clearSpace($value),
            ]);
        }

        return $this->redirect(route('product.show', $this->product->id));
    }

    public function render()
    {
        return view('livewire.admin.product.options')->layout('components.layouts.main');
    }
}
