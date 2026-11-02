<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class StockAdjustmentController extends Controller
{
    public function index(): View { return view('adjustments.index', ['adjustments' => StockAdjustment::with('items.batch.product')->latest()->paginate(15)]); }
    public function create(): View { return view('adjustments.create', ['batches' => StockBatch::with('product')->where('quantity', '>', 0)->orderBy('expires_at')->get()]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required','in:spoilage,damage,loss'], 'notes' => ['nullable','string','max:1000'],
            'items' => ['required','array','min:1'], 'items.*.stock_batch_id' => ['required','exists:stock_batches,id'], 'items.*.quantity' => ['required','integer','min:1'],
        ]);
        $adjustment = DB::transaction(function () use ($data) {
            $adjustment = StockAdjustment::create(['reference_number' => 'ADJ-'.now()->format('Ymd-His').'-'.strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)), 'reason' => $data['reason'], 'notes' => $data['notes'] ?? null, 'status' => 'pending', 'requested_by' => auth()->id()]);
            foreach ($data['items'] as $line) $adjustment->items()->create($line);
            return $adjustment;
        });
        ActivityLog::record('Adjustments', 'Requested', 'Submitted adjustment request '.$adjustment->reference_number.'.');
        return redirect()->route('adjustments.index')->with('status', 'Adjustment request submitted for approval.');
    }

    public function approve(StockAdjustment $adjustment): RedirectResponse
    {
        if ($adjustment->status !== 'pending') return back()->withErrors(['adjustment' => 'Only pending adjustments can be approved.']);
        try {
            DB::transaction(function () use ($adjustment) {
                $request = StockAdjustment::whereKey($adjustment->id)->lockForUpdate()->firstOrFail();
                if ($request->status !== 'pending') throw new RuntimeException('This adjustment was already reviewed.');
                $request->load('items');
                foreach ($request->items as $item) {
                    $batch = StockBatch::whereKey($item->stock_batch_id)->lockForUpdate()->firstOrFail();
                    if ($batch->quantity < $item->quantity) throw new RuntimeException('Requested adjustment exceeds the available quantity in batch '.$batch->batch_number.'.');
                    $batch->decrement('quantity', $item->quantity);
                    Product::whereKey($batch->product_id)->decrement('quantity', $item->quantity);
                    StockMovement::create(['product_id' => $batch->product_id, 'stock_batch_id' => $batch->id, 'user_id' => auth()->id(), 'movement_type' => 'adjustment_out', 'quantity' => $item->quantity, 'reference_number' => $request->reference_number, 'notes' => ucfirst($request->reason).($request->notes ? ': '.$request->notes : ''), 'moved_at' => now()]);
                }
                $request->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
            });
        } catch (RuntimeException $exception) { return back()->withErrors(['adjustment' => $exception->getMessage()]); }
        ActivityLog::record('Adjustments', 'Approved', 'Approved adjustment '.$adjustment->reference_number.'.');
        return back()->with('status', 'Adjustment approved and inventory updated.');
    }

    public function reject(StockAdjustment $adjustment): RedirectResponse
    {
        if ($adjustment->status !== 'pending') return back()->withErrors(['adjustment' => 'Only pending adjustments can be rejected.']);
        $adjustment->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        ActivityLog::record('Adjustments', 'Rejected', 'Rejected adjustment '.$adjustment->reference_number.'.');
        return back()->with('status', 'Adjustment request rejected. Inventory was unchanged.');
    }
}
