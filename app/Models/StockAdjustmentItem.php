<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    protected $fillable = ['adjustment_id', 'stock_batch_id', 'quantity'];
    public function batch(): BelongsTo { return $this->belongsTo(StockBatch::class, 'stock_batch_id'); }
}
