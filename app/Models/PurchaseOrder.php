<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['reference_number', 'supplier_id', 'status', 'expected_at', 'notes', 'created_by'];
    protected function casts(): array { return ['expected_at' => 'date']; }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class)->withTrashed(); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function stockIns(): HasMany { return $this->hasMany(StockIn::class); }
}
