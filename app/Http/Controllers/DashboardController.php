<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $hasProductsTable = Schema::hasTable('products');
        $hasStockBatchesTable = Schema::hasTable('stock_batches');
        $hasAdjustmentsTable = Schema::hasTable('stock_adjustments');

        if (! $hasProductsTable || ! $hasStockBatchesTable || ! $hasAdjustmentsTable) {
            return view('dashboard', [
                'productCount' => 0,
                'inStockCount' => 0,
                'lowStockCount' => 0,
                'outOfStockCount' => 0,
                'inventoryValue' => 0,
                'costValue' => 0,
                'activeLots' => 0,
                'pendingAdjustments' => 0,
                'expiring30DayCount' => 0,
                'user' => $user,
            ]);
        }

        $products = Product::query()->orderBy('name')->get();
        $batches = StockBatch::query()->with('product')->where('status', 'active')->where('quantity', '>', 0)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
            ->orderByRaw('expires_at IS NULL')->orderBy('expires_at')->get();
        $availableByProduct = $batches->groupBy('product_id')->map(fn ($lots) => $lots->sum('quantity'));
        $products->each(fn ($product) => $product->setAttribute('available_quantity', (int) $availableByProduct->get($product->id, 0)));
        $inStockProducts = $products->filter(fn ($product) => $product->available_quantity > $product->minimum_stock)->count();
        $lowStockProducts = $products->filter(fn ($product) => $product->available_quantity > 0 && $product->available_quantity <= $product->minimum_stock)->count();
        $outOfStockProducts = $products->filter(fn ($product) => $product->available_quantity <= 0)->count();
        $expiring30DayCount = $batches->filter(fn ($batch) => $batch->expires_at && $batch->expires_at->between(today(), today()->addDays(30)))->count();
        $isAdmin = $user?->role === 'admin';

        return view('dashboard', [
            'productCount' => $products->count(),
            'inStockCount' => $inStockProducts,
            'lowStockCount' => $lowStockProducts,
            'outOfStockCount' => $outOfStockProducts,
            'inventoryValue' => $products->sum(fn ($product) => $product->available_quantity * $product->selling_price),
            'costValue' => $batches->sum(fn ($batch) => $batch->quantity * $batch->unit_cost),
            'activeLots' => $batches->count(),
            'pendingAdjustments' => StockAdjustment::where('status', 'pending')
                ->when(! $isAdmin, fn ($query) => $query->where('requested_by', optional($user)->id))
                ->count(),
            'expiring30DayCount' => $expiring30DayCount,
            'user' => $user,
        ]);
    }
}
