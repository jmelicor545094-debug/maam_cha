<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $fillable = ['reference_number', 'reason', 'notes', 'status', 'requested_by', 'reviewed_by', 'reviewed_at'];
    protected function casts(): array { return ['reviewed_at' => 'datetime']; }
    public function items(): HasMany { return $this->hasMany(StockAdjustmentItem::class, 'adjustment_id'); }
}
