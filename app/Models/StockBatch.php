<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    protected $fillable = ['product_id', 'batch_number', 'quantity', 'unit_cost', 'expires_at', 'status'];

    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2', 'expires_at' => 'date'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
