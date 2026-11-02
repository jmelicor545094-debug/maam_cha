<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Rule;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View { return view('purchase-orders.index', ['orders' => PurchaseOrder::with('supplier')->withCount('items')->latest()->paginate(15)]); }
    public function create(): View { return view('purchase-orders.create', ['suppliers' => Supplier::orderBy('company_name')->get(), 'products' => Product::orderBy('name')->get(), 'lowStockProducts' => Product::whereColumn('quantity', '<=', 'minimum_stock')->orderBy('name')->get()]); }
    public function show(PurchaseOrder $purchaseOrder): View { $purchaseOrder->load('supplier', 'items.product'); return view('purchase-orders.show', compact('purchaseOrder')); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'expected_at' => ['nullable','date','after_or_equal:today'], 'notes' => ['nullable','string','max:1000'],
            'items' => ['required','array','min:1'], 'items.*.product_id' => ['required',Rule::exists('products','id')->whereNull('deleted_at')],
            'items.*.quantity_ordered' => ['required','integer','min:1'], 'items.*.unit_cost' => ['required','numeric','min:0'],
        ]);
        $order = DB::transaction(function () use ($data) {
            $order = PurchaseOrder::create(['reference_number' => 'PO-'.now()->format('Ymd-His').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)), 'supplier_id' => $data['supplier_id'], 'expected_at' => $data['expected_at'] ?? null, 'notes' => $data['notes'] ?? null, 'status' => 'draft', 'created_by' => auth()->id()]);
            foreach ($data['items'] as $item) $order->items()->create($item);
            return $order;
        });
        ActivityLog::record('Purchase Orders', 'Created', 'Created draft purchase order '.$order->reference_number.'.');
        return redirect()->route('purchase-orders.show', $order)->with('status', 'Purchase order saved as draft.');
    }

    public function markOrdered(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status !== 'draft') return back()->withErrors(['purchase_order' => 'Only draft orders can be marked as ordered.']);
        $purchaseOrder->update(['status' => 'ordered']);
        ActivityLog::record('Purchase Orders', 'Ordered', 'Marked purchase order '.$purchaseOrder->reference_number.' as ordered.');
        return back()->with('status', 'Purchase order marked as ordered.');
    }

    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! in_array($purchaseOrder->status, ['draft', 'ordered'], true)) return back()->withErrors(['purchase_order' => 'This purchase order can no longer be cancelled.']);
        if ($purchaseOrder->stockIns()->where('status', 'approved')->exists()) return back()->withErrors(['purchase_order' => 'An order with approved deliveries cannot be cancelled.']);
        $purchaseOrder->update(['status' => 'cancelled']);
        ActivityLog::record('Purchase Orders', 'Cancelled', 'Cancelled purchase order '.$purchaseOrder->reference_number.'.');
        return back()->with('status', 'Purchase order cancelled.');
    }
}
