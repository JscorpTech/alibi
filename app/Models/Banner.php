<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'title',
        'subtitle',
        'link',
        'image',
        'position',
        'status',
        'link_text',
        'type',
    ];
}
