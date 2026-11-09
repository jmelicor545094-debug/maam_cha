<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\SalesOrderPayment;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class SalesOrderController extends Controller
{
    public function index(): View { return view('sales-orders.index', ['orders' => SalesOrder::withCount('items')->latest()->paginate(15)]); }
    public function create(): View { return view('sales-orders.create', ['products' => Product::orderBy('name')->get()]); }
    public function show(SalesOrder $salesOrder): View { $salesOrder->load('items.product', 'payments'); return view('sales-orders.show', compact('salesOrder')); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required','string','max:160'], 'delivery_method' => ['required','in:delivery,pickup'], 'notes' => ['nullable','string','max:1000'],
            'items' => ['required','array','min:1'], 'items.*.product_id' => ['required',Rule::exists('products','id')->whereNull('deleted_at')], 'items.*.quantity' => ['required','integer','min:1'],
        ]);
        $order = DB::transaction(function () use ($data) {
            $order = SalesOrder::create(['order_number' => 'SO-'.now()->format('Ymd-His').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)), 'customer_name' => $data['customer_name'], 'delivery_method' => $data['delivery_method'], 'notes' => $data['notes'] ?? null, 'status' => 'draft', 'created_by' => auth()->id()]);
            foreach ($data['items'] as $line) {
                $product = Product::findOrFail($line['product_id']);
                $order->items()->create(['product_id' => $product->id, 'quantity' => $line['quantity'], 'unit_price' => $product->selling_price]);
            }
            $order->load('items');
            $subtotal = $order->items->sum(fn ($item) => $item->quantity * $item->unit_price);
            $taxAmount = round($subtotal * 0.12, 2);
            $order->update(['tax_rate' => 12, 'subtotal' => $subtotal, 'tax_amount' => $taxAmount, 'total_amount' => $subtotal + $taxAmount]);
            return $order;
        });
        ActivityLog::record('Sales Orders', 'Created', 'Created draft order '.$order->order_number.'.');
        return redirect()->route('sales-orders.show', $order)->with('status', 'Sales order created as draft.');
    }

    public function recordPayment(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $data = $request->validate(['amount_tendered' => ['required','numeric','gt:0'], 'payment_method' => ['required','in:cash,bank_transfer,check']]);
        if (! in_array($salesOrder->status, ['confirmed', 'dispensed'], true)) return back()->withErrors(['payment' => 'Payments can be recorded for confirmed or dispensed orders.']);
        try {
            DB::transaction(function () use ($data, $salesOrder) {
                $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
                $balance = max(0, (float) $order->total_amount - (float) $order->amount_paid);
                if ($balance <= 0) throw new RuntimeException('This order is already fully paid.');
                $applied = min((float) $data['amount_tendered'], $balance);
                SalesOrderPayment::create(['sales_order_id' => $order->id, 'received_by' => auth()->id(), 'amount_tendered' => $data['amount_tendered'], 'amount_applied' => $applied, 'change_due' => max(0, (float) $data['amount_tendered'] - $applied), 'payment_method' => $data['payment_method'], 'paid_at' => now()]);
                $newPaid = (float) $order->amount_paid + $applied;
                $order->update(['amount_paid' => $newPaid, 'payment_status' => $newPaid >= (float) $order->total_amount ? 'paid' : 'partial']);
            });
        } catch (RuntimeException $exception) { return back()->withErrors(['payment' => $exception->getMessage()]); }
        ActivityLog::record('Sales Orders', 'Payment', 'Recorded payment for '.$salesOrder->order_number.'.');
        return back()->with('status', 'Payment recorded.');
    }

    public function confirm(SalesOrder $salesOrder): RedirectResponse
    {
        if ($salesOrder->status !== 'draft') return back()->withErrors(['order' => 'Only draft orders can be confirmed.']);
        $salesOrder->update(['status' => 'confirmed']);
        ActivityLog::record('Sales Orders', 'Confirmed', 'Confirmed order '.$salesOrder->order_number.'.');
        return back()->with('status', 'Order confirmed and ready to dispense.');
    }

    public function dispense(SalesOrder $salesOrder): RedirectResponse
    {
        if ($salesOrder->status !== 'confirmed') return back()->withErrors(['order' => 'Only confirmed orders can be dispensed.']);
        try {
            DB::transaction(function () use ($salesOrder) {
                $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
                if ($order->status !== 'confirmed') throw new RuntimeException('This order was already reviewed.');
                $order->load('items.product');
                foreach ($order->items as $item) {
                    $remaining = $item->quantity;
                    $batches = StockBatch::query()->where('product_id', $item->product_id)->where('status', 'active')->where('quantity', '>', 0)
                        ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
                        ->orderByRaw('expires_at IS NULL')->orderBy('expires_at')->lockForUpdate()->get();
                    foreach ($batches as $batch) {
                        if ($remaining === 0) break;
                        $taken = min($remaining, $batch->quantity);
                        $batch->decrement('quantity', $taken);
                        StockMovement::create(['product_id' => $item->product_id, 'stock_batch_id' => $batch->id, 'user_id' => auth()->id(), 'movement_type' => 'stock_out', 'quantity' => $taken, 'reference_number' => $order->order_number, 'notes' => 'FEFO sales order dispensing', 'moved_at' => now()]);
                        $remaining -= $taken;
                    }
                    if ($remaining > 0) throw new RuntimeException('Insufficient non-expired batch stock for '.$item->product->name.'.');
                    Product::whereKey($item->product_id)->decrement('quantity', $item->quantity);
                }
                $order->update(['status' => 'dispensed', 'dispensed_at' => now()]);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['order' => $exception->getMessage()]);
        }
        ActivityLog::record('Sales Orders', 'Dispensed', 'Dispensed order '.$salesOrder->order_number.' using FEFO batches.');
        return back()->with('status', 'Order dispensed. Stock was deducted by earliest expiry first.');
    }

    public function cancel(SalesOrder $salesOrder): RedirectResponse
    {
        if (! in_array($salesOrder->status, ['draft', 'confirmed'], true)) return back()->withErrors(['order' => 'Only draft or confirmed orders can be cancelled.']);
        if ((float) $salesOrder->amount_paid > 0) return back()->withErrors(['order' => 'This order has recorded payments and cannot be cancelled.']);
        $salesOrder->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        ActivityLog::record('Sales Orders', 'Cancelled', 'Cancelled order '.$salesOrder->order_number.'.');
        return back()->with('status', 'Order cancelled. No stock was deducted.');
    }
}
