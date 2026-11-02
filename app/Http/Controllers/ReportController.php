<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $type = $request->query('type');
        if (in_array($type, ['expired-items', 'low-stock', 'expiring-soon'], true)) {
            if ($type === 'expired-items') {
                $items = StockBatch::with('product')->whereIn('status', ['active', 'expired'])->where('quantity', '>', 0)
                    ->whereDate('expires_at', '<', today())->orderBy('expires_at')->get();
            } elseif ($type === 'low-stock') {
                $items = Product::query()->withSum(['batches as available_quantity' => fn ($query) => $query
                    ->where('status', 'active')->where('quantity', '>', 0)
                    ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))], 'quantity')
                    ->orderBy('name')->get()->filter(fn ($product) => (int) $product->available_quantity <= $product->minimum_stock)->values();
            } else {
                $items = StockBatch::with('product')->where('status', 'active')->where('quantity', '>', 0)
                    ->whereBetween('expires_at', [today(), today()->addDays(90)])->orderBy('expires_at')->get();
            }

            return view('reports.inventory-list', compact('items', 'type'));
        }

        $products = Product::with('batches')->orderBy('name')->get();
        $batches = StockBatch::with('product')->where('quantity', '>', 0)->orderBy('expires_at')->get();
        $sales = SalesOrder::with('items')->where('status', 'dispensed')->latest('dispensed_at')->take(20)->get();
        $movements = StockMovement::with('product', 'batch', 'user')->latest('moved_at')->take(30)->get();
        $suppliers = Supplier::withCount(['stockIns as approved_deliveries_count' => fn ($query) => $query->where('status', 'approved')])
            ->withMax(['stockIns as latest_approved_delivery' => fn ($query) => $query->where('status', 'approved')], 'received_at')->orderBy('company_name')->get();
        return view('reports.index', compact('products', 'batches', 'sales', 'movements', 'suppliers'));
    }
}
