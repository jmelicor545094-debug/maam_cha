<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity_ordered', 'quantity_received', 'unit_cost', 'discrepancy_notes'];
    protected function casts(): array { return ['unit_cost' => 'decimal:2']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
