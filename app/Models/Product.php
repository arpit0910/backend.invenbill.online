<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'category_id',
        'product_code',
        'name',
        'sku',
        'mrp',
        'sale_price',
        'short_description',
        'detailed_description',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->product_code)) {
                do {
                    $code = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
                } while (static::where('product_code', $code)->exists());
                $product->product_code = $code;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function mainImage()
    {
        return $this->images()->where('is_main', true)->first();
    }
}
