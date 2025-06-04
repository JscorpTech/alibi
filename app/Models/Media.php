<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'name',
        'path',
        'taggable',
        'mime_type',
    ];

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
