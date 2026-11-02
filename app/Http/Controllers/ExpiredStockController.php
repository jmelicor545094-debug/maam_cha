<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpiredStockController extends Controller
{
    public function index(): View
    {
        $batches = StockBatch::with('product')->where('status', 'active')->where('quantity', '>', 0)->whereDate('expires_at', '<', today())->orderBy('expires_at')->paginate(20);
        return view('expired-stock.index', compact('batches'));
    }

    public function cleanup(): RedirectResponse
    {
        $count = 0;
        DB::transaction(function () use (&$count) {
            $expired = StockBatch::where('status', 'active')->where('quantity', '>', 0)->whereDate('expires_at', '<', today())->lockForUpdate()->get();
            foreach ($expired as $batch) {
                $quantity = $batch->quantity;
                $batch->update(['status' => 'expired']);
                Product::whereKey($batch->product_id)->decrement('quantity', $quantity);
                StockMovement::create(['product_id' => $batch->product_id, 'stock_batch_id' => $batch->id, 'user_id' => auth()->id(), 'movement_type' => 'expiry_writeoff', 'quantity' => $quantity, 'reference_number' => 'EXP-'.$batch->id, 'notes' => 'Expired inventory cleanup', 'moved_at' => now()]);
                $count++;
            }
        });
        ActivityLog::record('Expired Stock', 'Cleanup', 'Marked '.$count.' expired batch(es) inactive and recorded write-offs.');
        return redirect()->route('expired-stock.index')->with('status', $count.' expired batch(es) cleared from available inventory. Records were retained for audit.');
    }
}
