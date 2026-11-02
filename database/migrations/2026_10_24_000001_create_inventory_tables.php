<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('category');
            $table->string('unit')->default('piece');
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->index(['product_id', 'expires_at']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('products');
    }
};
