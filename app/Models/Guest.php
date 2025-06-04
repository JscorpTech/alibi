<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory, BaseModel;

    public $fillable = [
        "name",
        "token",
    ];

    public $hidden = [
        "token",
    ];
}
