<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class StockInController extends Controller
{
    public function index(): View { return view('stock-ins.index', ['stockIns' => StockIn::with('supplier')->withCount('items')->latest()->paginate(15)]); }
    public function create(Request $request): View { return view('stock-ins.create', ['suppliers' => Supplier::orderBy('company_name')->get(), 'products' => Product::orderBy('name')->get(), 'purchaseOrders' => PurchaseOrder::with('supplier')->whereIn('status', ['ordered', 'partially_received'])->latest()->get(), 'selectedPurchaseOrderId' => $request->query('purchase_order')]); }

    public function store(Request $request): RedirectResponse
    {
        $referenceNumber = trim((string) $request->input('reference_number', ''));
        if ($referenceNumber === '') {
            $referenceNumber = 'SI-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }
        $request->merge(['reference_number' => $referenceNumber]);

        $items = $request->input('items', []);
        foreach ($items as $index => $item) {
            $batchNumber = trim((string) ($item['batch_number'] ?? ''));
            if ($batchNumber === '') {
                $items[$index]['batch_number'] = 'BATCH-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            }
        }
        $request->merge(['items' => $items]);

        $data = $request->validate([
            'reference_number' => ['required','string','max:80','unique:stock_ins,reference_number'],
            'supplier_id' => ['required', Rule::exists('suppliers','id')->whereNull('deleted_at')], 'purchase_order_id' => ['nullable','exists:purchase_orders,id'], 'received_at' => ['required','date'], 'notes' => ['nullable','string','max:1000'],
            'items' => ['required','array','min:1'], 'items.*.product_id' => ['required',Rule::exists('products','id')->whereNull('deleted_at')],
            'items.*.batch_number' => ['required','string','max:80'], 'items.*.quantity' => ['required','integer','min:1'],
            'items.*.unit_cost' => ['required','numeric','min:0'], 'items.*.expires_at' => ['nullable','date','after:today'],
        ]);
        if (! empty($data['purchase_order_id'])) {
            $purchaseOrder = PurchaseOrder::findOrFail($data['purchase_order_id']);
            if ($purchaseOrder->supplier_id != $data['supplier_id'] || ! in_array($purchaseOrder->status, ['ordered', 'partially_received'], true)) {
                return back()->withErrors(['purchase_order_id' => 'Choose an open purchase order from the selected supplier.'])->withInput();
            }
        }
        DB::transaction(function () use ($data) {
            $header = StockIn::create(collect($data)->except('items')->all() + ['status' => 'pending', 'created_by' => auth()->id()]);
            foreach ($data['items'] as $item) $header->items()->create($item);
        });
        ActivityLog::record('Stock-in', 'Created', 'Recorded pending delivery '.$data['reference_number'].'.');
        return redirect()->route('stock-ins.index')->with('status', 'Stock-in recorded and awaiting approval.');
    }

    public function approve(StockIn $stockIn): RedirectResponse
    {
        if ($stockIn->status !== 'pending') return back()->withErrors(['stock_in' => 'Only pending stock-ins can be approved.']);
        try {
            DB::transaction(function () use ($stockIn) {
            $entry = StockIn::whereKey($stockIn->id)->lockForUpdate()->firstOrFail();
            if ($entry->status !== 'pending') throw new RuntimeException('This stock-in was already reviewed.');
            $entry->load('items');
            foreach ($entry->items as $item) {
                $batch = StockBatch::firstOrCreate(
                    ['product_id' => $item->product_id, 'batch_number' => $item->batch_number],
                    ['quantity' => 0, 'unit_cost' => $item->unit_cost, 'expires_at' => $item->expires_at, 'status' => 'active']
                );
                $batch->increment('quantity', $item->quantity);
                Product::whereKey($item->product_id)->increment('quantity', $item->quantity);
                StockMovement::create(['product_id' => $item->product_id, 'stock_batch_id' => $batch->id, 'user_id' => auth()->id(), 'movement_type' => 'stock_in', 'quantity' => $item->quantity, 'reference_number' => $stockIn->reference_number, 'notes' => 'Approved supplier delivery', 'moved_at' => now()]);
                if ($entry->purchase_order_id) {
                    $orderItem = PurchaseOrderItem::where('purchase_order_id', $entry->purchase_order_id)->where('product_id', $item->product_id)->lockForUpdate()->first();
                    if ($orderItem) {
                        $received = $orderItem->quantity_received + $item->quantity;
                        $orderItem->update(['quantity_received' => $received, 'discrepancy_notes' => $received > $orderItem->quantity_ordered ? 'Received quantity exceeds ordered quantity.' : $orderItem->discrepancy_notes]);
                    }
                }
            }
            $entry->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
            if ($entry->purchase_order_id) {
                $order = PurchaseOrder::with('items')->findOrFail($entry->purchase_order_id);
                $complete = $order->items->every(fn ($line) => $line->quantity_received >= $line->quantity_ordered);
                $order->update(['status' => $complete ? 'received' : 'partially_received']);
            }
            });
        } catch (RuntimeException $exception) { return back()->withErrors(['stock_in' => $exception->getMessage()]); }
        ActivityLog::record('Stock-in', 'Approved', 'Approved delivery '.$stockIn->reference_number.' and updated batch inventory.');
        return back()->with('status', 'Stock-in approved and inventory updated.');
    }
}
