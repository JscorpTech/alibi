<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'title',
        'message',
        "is_register",
    ];
}
