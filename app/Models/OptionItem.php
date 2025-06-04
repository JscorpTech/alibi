<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionItem extends Model
{
    use HasFactory, BaseModel;
    public $fillable = [
        "option_id",
        "name",
        "desc",
    ];

    
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
