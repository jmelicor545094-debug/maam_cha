<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id', 'stock_batch_id', 'movement_type', 'quantity', 'reference_number', 'notes', 'moved_at'];
    protected function casts(): array { return ['moved_at' => 'datetime']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class)->withTrashed(); }
    public function batch(): BelongsTo { return $this->belongsTo(StockBatch::class, 'stock_batch_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
