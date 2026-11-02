<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id(); $table->string('order_number')->unique(); $table->string('customer_name');
            $table->string('delivery_method'); $table->text('notes')->nullable(); $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('dispensed_at')->nullable(); $table->timestamp('cancelled_at')->nullable(); $table->timestamps();
        });
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity'); $table->decimal('unit_price', 12, 2); $table->timestamps();
        });
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained()->restrictOnDelete(); $table->foreignId('stock_batch_id')->nullable()->constrained('stock_batches')->nullOnDelete(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('movement_type'); $table->unsignedInteger('quantity'); $table->string('reference_number'); $table->text('notes')->nullable(); $table->timestamp('moved_at')->index();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_movements'); Schema::dropIfExists('sales_order_items'); Schema::dropIfExists('sales_orders'); }
};
