<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockAdjustment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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
        $isAdmin = auth()->user()->role === 'admin';

        return view('dashboard', [
            'productCount' => $products->count(),
            'inStockCount' => $inStockProducts,
            'lowStockCount' => $lowStockProducts,
            'outOfStockCount' => $outOfStockProducts,
            'inventoryValue' => $products->sum(fn ($product) => $product->available_quantity * $product->selling_price),
            'costValue' => $batches->sum(fn ($batch) => $batch->quantity * $batch->unit_cost),
            'activeLots' => $batches->count(),
            'pendingAdjustments' => StockAdjustment::where('status', 'pending')
                ->when(! $isAdmin, fn ($query) => $query->where('requested_by', auth()->id()))
                ->count(),
            'expiring30DayCount' => $expiring30DayCount,
        ]);
    }
}
