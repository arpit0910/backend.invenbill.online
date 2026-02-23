<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'warehouse_code',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'timezone',
        'opening_time',
        'closing_time',
        'weekly_off_day',
        'status',
        'manager_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
