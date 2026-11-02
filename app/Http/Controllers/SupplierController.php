<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View { return view('suppliers.index', ['suppliers' => Supplier::orderBy('company_name')->paginate(15)]); }
    public function archived(): View { return view('suppliers.archived', ['suppliers' => Supplier::onlyTrashed()->orderBy('company_name')->paginate(15)]); }
    public function create(): View { return view('suppliers.form', ['supplier' => new Supplier()]); }
    public function store(Request $request): RedirectResponse { $supplier = Supplier::create($this->validated($request)); ActivityLog::record('Suppliers', 'Created', 'Created supplier '.$supplier->company_name.'.'); return redirect()->route('suppliers.index')->with('status', 'Supplier added.'); }
    public function edit(Supplier $supplier): View { return view('suppliers.form', compact('supplier')); }
    public function update(Request $request, Supplier $supplier): RedirectResponse { $supplier->update($this->validated($request)); ActivityLog::record('Suppliers', 'Updated', 'Updated supplier '.$supplier->company_name.'.'); return redirect()->route('suppliers.index')->with('status', 'Supplier updated.'); }
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete(); ActivityLog::record('Suppliers', 'Archived', 'Archived supplier '.$supplier->company_name.'.'); return redirect()->route('suppliers.index')->with('status', 'Supplier archived.');
    }
    public function restore(int $supplierId): RedirectResponse
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($supplierId); $supplier->restore();
        ActivityLog::record('Suppliers', 'Restored', 'Restored supplier '.$supplier->company_name.'.');
        return redirect()->route('suppliers.archived')->with('status', 'Supplier restored.');
    }
    private function validated(Request $request): array { return $request->validate(['company_name' => ['required','string','max:160'], 'contact_person' => ['nullable','string','max:120'], 'phone' => ['nullable','string','max:40'], 'email' => ['nullable','email','max:160'], 'address' => ['nullable','string','max:1000'], 'city' => ['nullable','string','max:120'], 'country' => ['nullable','string','max:120'], 'delivery_schedule' => ['nullable','string','max:160']]); }
}
