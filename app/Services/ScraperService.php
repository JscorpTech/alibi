<?php

namespace App\Services;

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use IProgrammer\ProgressBar;
use JetBrains\PhpStorm\NoReturn;
use stdClass;

class ScraperService
{
    public Client $client;
    /**
     * @var array|string[]
     */
    public array|string $headers;
    public string $key;

    public function __construct()
    {
        $this->client = new Client();
        $this->headers = [
            'Content-Type' => 'application/json',
        ];
        $this->key = Env::get('OX_KEY');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    #[NoReturn]
    public function getProducts(): void
    {
        $response = $this->request('https://baffoo.ox-sys.com/cash-desk-api/getobject/product', data: [
            'key'       => $this->key,
            'timestamp' => 1681811207,
            'offset'    => 0,
            'limit'     => null,
            'resource'  => null,
        ]);

        foreach ($response->data as $index => $datum) {
            try {
                try {
                    ProgressBar::run($index + 1, count($response->data));
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }

                $products = $this->getProduct($datum->id); // Get product detail

                $sizes = []; // Product sizes
                $colors = []; // Product colors
                $name = $datum->name; // Get product name
                $sku = $datum->sku; // Get product sku
                $price = $products[0]->stocks[0]->sellPrice->UZS; // Product price

                /** @var array|object $color_ids */
                /** @var array|object $size_ids */
                $color_ids = []; // Database color id
                $size_ids = []; // Database size id

                /** @var array|object $color_images */
                $color_images = []; // Database color images id
                $key = $datum->id; // Product key

                foreach ($products as $product) {
                    $size = array_filter($product->properties, function ($el) {
                        return $el->name == 'Размер';
                    }); // get Product variant sizes

                    $color = array_filter($product->properties, function ($el) {
                        return $el->name == 'Цвет';
                    }); // get Product variant colors

                    $sizes = array_merge(array_column($size, 'value'), $sizes); // Array marge for sizes
                    $colors = array_merge(array_column($color, 'value'), $colors); // Array marge for colors
                }

                foreach ($sizes as $size) {
                    $size_ids[] = Size::query()->firstOrCreate(['name' => $size], ['name' => $size])->id; // Create size for not found size
                }
                foreach ($colors as $color) {
                    $color_ids[] = Color::query()->firstOrCreate(['name' => $color], ['name' => $color])->id; // Create color for not found color
                }

                $product = Product::query()->firstOrCreate(['key' => $key], [
                    'image'    => 'products/default.jpeg',
                    'name_ru'  => $name,
                    'desc_ru'  => '123',
                    'gender'   => GenderEnum::MALE,
                    'price'    => $price,
                    'discount' => 0,
                    'count'    => 1,
                    'sku'      => $sku,
                    'status'   => ProductStatusEnum::NOT_AVAILABLE,
                ]);

                $product->sizes()->sync($size_ids);

                try {
                    foreach ($color_ids as $color_id) {
                        $color = $product->colors()->firstOrCreate(['color_id' => $color_id], ['color_id' => $color_id]);
                        if (!$color->image) {
                            $response = $color->image()->create([
                                'path' => 'products/default.jpeg',
                            ]);
                        }
                        $color_images[] = $color->id;
                    }
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }

                foreach ($color_ids as $color_id) {
                    foreach ($size_ids as $size_id) {
                        $product->options()->firstOrCreate(['color_id' => $color_id, 'size_id' => $size_id], ['color_id' => $color_id, 'size_id' => $size_id, 'count' => 1]);
                    }
                }
                sleep(1);
            } catch (\Throwable $e) {
                Log::error($e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getProduct(int $product_id): array|stdClass
    {
        return $this->request('https://baffoo.ox-sys.com/cash-desk-api/getobject/variation', data: [
            'key'       => $this->key,
            'timestamp' => 1681811207,
            'offset'    => 0,
            'limit'     => 1,
            'resource'  => $product_id,
        ])->data;
    }

    /**
     * Another functions
     * @throws Exception
     */
    public function request($url = null, $data = null, $headers = null, string $method = 'POST'): array|stdClass
    {
        $headers = $headers ?? $this->headers;
        $url = $url ?? $this->url;
        $data = $data ?? $this->data;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        try {
            return json_decode($response);
        } catch (\Throwable $e) {
            throw new Exception('Response parse json error: ' . $e->getMessage());
        }
    }
}
