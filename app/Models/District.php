<?php

namespace App\Models;

use App\Http\Interfaces\LocaleInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model implements LocaleInterface
{
    use HasFactory;
    use BaseModel;

    public function localeFields(): array
    {
        return [
            'name',
        ];
    }

    protected $fillable = [
        'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
