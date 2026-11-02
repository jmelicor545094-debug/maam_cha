<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@medcare.local'], ['name' => 'Medcare Administrator', 'password' => 'password', 'role' => 'admin']);
        foreach ([
            'create-product', 'update-product', 'destroy-product',
            'create-category', 'update-category', 'destroy-category',
            'create-supplier', 'update-supplier', 'destroy-supplier',
            'create-user', 'update-user', 'destroy-user',
            'create-purchase', 'update-purchase', 'destroy-purchase',
            'approve-stock-in', 'approve-adjustment', 'dispense-sales-order', 'view-reports',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        Supplier::updateOrCreate(['company_name' => 'Davao Medical Wholesale'], ['contact_person' => 'Demo Contact', 'email' => 'orders@example.test', 'phone' => '+63 82 000 0000', 'address' => 'Davao City']);
        $catalog = [
            ['sku' => 'MED-001', 'name' => 'Paracetamol 500mg', 'brand' => 'Medcare Generic', 'category' => 'Generic medicine', 'unit' => 'box', 'selling_price' => 85, 'minimum_stock' => 40, 'quantity' => 120],
            ['sku' => 'MED-002', 'name' => 'Amoxicillin 500mg', 'brand' => 'Medcare Generic', 'category' => 'Antibiotic', 'unit' => 'box', 'selling_price' => 240, 'minimum_stock' => 30, 'quantity' => 18],
            ['sku' => 'SUP-014', 'name' => 'Disposable examination gloves', 'brand' => 'SafeHands', 'category' => 'Medical supplies', 'unit' => 'box', 'selling_price' => 160, 'minimum_stock' => 25, 'quantity' => 12],
            ['sku' => 'MED-027', 'name' => 'Cetirizine 10mg', 'brand' => 'Medcare Generic', 'category' => 'Generic medicine', 'unit' => 'box', 'selling_price' => 95, 'minimum_stock' => 20, 'quantity' => 75],
        ];

        foreach ($catalog as $index => $attributes) {
            $product = Product::updateOrCreate(['sku' => $attributes['sku']], $attributes);
            StockBatch::updateOrCreate([
                'product_id' => $product->id, 'batch_number' => 'DEMO-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            ], [
                'quantity' => $product->quantity,
                'unit_cost' => round($product->selling_price * 0.68, 2),
                'expires_at' => now()->addMonths($index === 1 ? 2 : 14),
            ]);
        }
    }
}
