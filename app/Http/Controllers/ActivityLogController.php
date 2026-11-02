<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')->when(auth()->user()->role !== 'admin', fn ($query) => $query->where('user_id', auth()->id()))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($q) => $q->where('module', 'like', '%'.$request->search.'%')->orWhere('action', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest('created_at')->paginate(25)->withQueryString();
        return view('activity-logs.index', compact('logs'));
    }
}
