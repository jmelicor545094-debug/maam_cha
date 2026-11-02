<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id(); $table->string('reference_number')->unique(); $table->string('reason'); $table->text('notes')->nullable(); $table->string('status')->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('reviewed_at')->nullable(); $table->timestamps();
        });
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete(); $table->foreignId('stock_batch_id')->constrained('stock_batches')->restrictOnDelete();
            $table->unsignedInteger('quantity'); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_adjustment_items'); Schema::dropIfExists('stock_adjustments'); }
};
