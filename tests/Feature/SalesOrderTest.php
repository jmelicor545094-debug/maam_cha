<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_sales_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'General', 'description' => 'Catalog category']);
        $product = Product::create([
            'sku' => 'SUP-001',
            'name' => 'Paracetamol',
            'brand' => null,
            'category_id' => $category->id,
            'category' => $category->name,
            'unit' => 'box',
            'selling_price' => 150.00,
            'minimum_stock' => 5,
            'quantity' => 40,
        ]);

        $response = $this->actingAs($user)->post('/sales-orders', [
            'customer_name' => 'Jane Doe',
            'delivery_method' => 'pickup',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_orders', ['customer_name' => 'Jane Doe']);
    }
}
