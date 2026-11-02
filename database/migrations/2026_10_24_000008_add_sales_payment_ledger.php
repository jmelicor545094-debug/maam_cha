<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) { $table->decimal('amount_paid', 12, 2)->default(0); $table->string('payment_status')->default('unpaid'); });
        Schema::create('sales_order_payments', function (Blueprint $table) {
            $table->id(); $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete(); $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount_tendered', 12, 2); $table->decimal('amount_applied', 12, 2); $table->decimal('change_due', 12, 2)->default(0); $table->string('payment_method')->default('cash'); $table->timestamp('paid_at')->index();
        });
    }
    public function down(): void { Schema::dropIfExists('sales_order_payments'); Schema::table('sales_orders', fn (Blueprint $table) => $table->dropColumn(['amount_paid', 'payment_status'])); }
};
