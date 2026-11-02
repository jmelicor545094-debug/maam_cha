<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['sku', 'name', 'brand', 'category', 'category_id', 'unit', 'selling_price', 'minimum_stock', 'quantity'];

    protected function casts(): array
    {
        return ['selling_price' => 'decimal:2'];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function categoryRecord(): BelongsTo { return $this->belongsTo(Category::class, 'category_id')->withTrashed(); }
}
