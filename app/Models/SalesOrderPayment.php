<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderPayment extends Model
{
    public $timestamps = false;
    protected $fillable = ['sales_order_id', 'received_by', 'amount_tendered', 'amount_applied', 'change_due', 'payment_method', 'paid_at'];
    protected function casts(): array { return ['amount_tendered' => 'decimal:2', 'amount_applied' => 'decimal:2', 'change_due' => 'decimal:2', 'paid_at' => 'datetime']; }
    public function order(): BelongsTo { return $this->belongsTo(SalesOrder::class, 'sales_order_id'); }
}
