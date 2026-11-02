<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInItem extends Model
{
    protected $fillable = ['stock_in_id', 'product_id', 'batch_number', 'quantity', 'unit_cost', 'expires_at'];
    protected function casts(): array { return ['unit_cost' => 'decimal:2', 'expires_at' => 'date']; }
    public function stockIn(): BelongsTo { return $this->belongsTo(StockIn::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class)->withTrashed(); }
}
