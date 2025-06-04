<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Product\ProductListResource;
use App\Http\Resources\Api\Product\ProductResource;
use App\Http\Resources\BaseResource;
use App\Models\VideoLike;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VideoResource extends JsonResource
{
    use BaseResource;


    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "video"=>Storage::url($this->path),
            "likes"=>VideoLike::query()->where(['video_id'=>$this->id])->count(),
            "product"=>ProductListResource::make($this->product),
        ];
    }
}
