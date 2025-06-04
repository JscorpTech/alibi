<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SizeInfo extends Model
{
    use HasFactory;
    use BaseModel;
    use SoftDeletes;

    public $fillable = [
        'image_1',
        'image_2',
        'name',
    ];
}
