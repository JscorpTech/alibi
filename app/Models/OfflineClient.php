<?php

// app/Models/OfflineClient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'discount_percent',
        'discount_note',
        // ... если есть ещё поля
    ];

    protected $casts = [
        'discount' => 'integer',
        'discount_percent' => 'integer',
    ];

    public function orderGroups(): HasMany
    {
        return $this->hasMany(OrderGroup::class, 'offline_client_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'offline_client_id');
    }
}