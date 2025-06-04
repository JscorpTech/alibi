<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Video extends Model
{
    use HasFactory,BaseModel;

    public $table = "videos";

    public $fillable = [
        "product_id",
        "status",
        "path"
    ];
    
    public function product(): BelongsTo{
        return $this->belongsTo(Product::class,"product_id");
    }
}
