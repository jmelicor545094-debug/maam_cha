<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->text('description')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->softDeletes();
        });
        foreach (DB::table('products')->select('category')->distinct()->pluck('category')->filter() as $name) {
            $categoryId = DB::table('categories')->insertGetId(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('products')->where('category', $name)->update(['category_id' => $categoryId]);
        }

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('delivery_schedule')->nullable(); $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_number', 40)->nullable(); $table->date('birthday')->nullable(); $table->string('sex', 30)->nullable();
            $table->string('street')->nullable(); $table->string('city')->nullable(); $table->string('province')->nullable(); $table->string('zip_code', 20)->nullable(); $table->softDeletes();
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(12); $table->decimal('subtotal', 12, 2)->default(0); $table->decimal('tax_amount', 12, 2)->default(0); $table->decimal('total_amount', 12, 2)->default(0);
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id(); $table->string('reference_number')->unique(); $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('draft'); $table->date('expected_at')->nullable(); $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity_ordered'); $table->unsignedInteger('quantity_received')->default(0); $table->decimal('unit_cost', 12, 2); $table->text('discrepancy_notes')->nullable(); $table->timestamps();
        });
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->string('request_type'); $table->json('requested_data'); $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable(); $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('reviewed_at')->nullable(); $table->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->string('module'); $table->string('action'); $table->text('description'); $table->string('ip_address', 45)->nullable(); $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs'); Schema::dropIfExists('permission_requests');
        Schema::table('stock_ins', fn (Blueprint $table) => $table->dropConstrainedForeignId('purchase_order_id'));
        Schema::dropIfExists('purchase_order_items'); Schema::dropIfExists('purchase_orders');
        Schema::table('sales_orders', fn (Blueprint $table) => $table->dropColumn(['tax_rate', 'subtotal', 'tax_amount', 'total_amount']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['contact_number', 'birthday', 'sex', 'street', 'city', 'province', 'zip_code', 'deleted_at']));
        Schema::table('suppliers', fn (Blueprint $table) => $table->dropColumn(['delivery_schedule', 'deleted_at']));
        Schema::table('products', function (Blueprint $table) { $table->dropConstrainedForeignId('category_id'); $table->dropColumn('deleted_at'); });
        Schema::dropIfExists('categories');
    }
};
