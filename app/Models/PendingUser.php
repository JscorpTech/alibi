<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'phone',
        'full_name',
        'password',
        'fcm_token',
    ];
}
