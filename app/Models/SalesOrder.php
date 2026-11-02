<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $fillable = ['order_number', 'customer_name', 'delivery_method', 'notes', 'status', 'created_by', 'dispensed_at', 'cancelled_at', 'tax_rate', 'subtotal', 'tax_amount', 'total_amount', 'amount_paid', 'payment_status'];
    protected function casts(): array { return ['dispensed_at' => 'datetime', 'cancelled_at' => 'datetime', 'tax_rate' => 'decimal:2', 'subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2']; }
    public function items(): HasMany { return $this->hasMany(SalesOrderItem::class); }
    public function payments(): HasMany { return $this->hasMany(SalesOrderPayment::class); }
}
