<?php

namespace App\Models;

use App\Http\Interfaces\LocaleInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model implements LocaleInterface
{
    use HasFactory;
    use BaseModel;

    public function localeFields(): array
    {
        return [
            'name',
        ];
    }

    protected $fillable = [];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'region_id');
    }
}
