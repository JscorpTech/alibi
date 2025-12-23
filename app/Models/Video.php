<?php

namespace App\Models;

use App\Jobs\ProcessVideoJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory, BaseModel;

    public $table = "videos";

    protected $fillable = [
        "product_id",
        "path",
        "converted_path",
        "thumbnail_path",
        "status",
    ];

    protected static function booted(): void
    {
        static::created(function (Video $video) {
            dispatch(new ProcessVideoJob($video));
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }
}