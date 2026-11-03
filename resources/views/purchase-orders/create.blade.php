@extends('layouts.app')
@section('title', 'Create purchase order')
@section('section', 'Purchase orders')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Procurement</div><h1>Create purchase order</h1><div class="muted">Suggested low-stock items are listed for quick replenishment.</div></div><a class="btn" href="{{ route('purchase-orders.index') }}">← Back to orders</a></div>
@if($lowStockProducts->isNotEmpty())<section class="panel" style="margin-bottom:15px"><div class="panel-head"><h2>Below minimum stock</h2><span class="muted">{{ $lowStockProducts->count() }} products</span></div><div style="padding:12px 16px;display:flex;gap:8px;flex-wrap:wrap">@foreach($lowStockProducts as $product)<span class="badge">{{ $product->name }} · {{ $product->quantity }}/{{ $product->minimum_stock }}</span>@endforeach</div></section>@endif
<section class="panel"><div class="panel-head"><h2>Order details</h2></div><div style="padding:10px 20px 18px"><form method="post" action="{{ route('purchase-orders.store') }}">@csrf<div class="form-grid"><div class="field"><label for="supplier_id">Supplier*</label><select id="supplier_id" name="supplier_id" required><option value="">Choose supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>@endforeach</select>@error('supplier_id')<span class="errors">{{ $message }}</span>@enderror</div><div class="field"><label for="expected_at">Expected delivery date</label><input id="expected_at" name="expected_at" type="date" min="{{ today()->format('Y-m-d') }}"></div><div class="field" style="grid-column:1/-1"><label for="notes">Notes</label><input id="notes" name="notes"></div></div>
<div class="panel-head" style="padding:15px 0"><h2>Products to order</h2><button class="btn" type="button" onclick="addLine()">＋ Add line</button></div><div id="items"><div class="form-grid item-line" style="border-bottom:1px solid #edf0f1"><div class="field"><label>Product*</label><select name="items[0][product_id]" required onchange="productChanged(this)"><option value="">Select product</option>@foreach($products as $product)<option value="{{ $product->id }}" data-unit-cost="{{ round((float) $product->selling_price * 0.68, 2) }}">{{ $product->sku }} · {{ $product->name }} ({{ $product->quantity }} on hand)</option>@endforeach</select></div><div class="field"><label>Quantity to order*</label><input type="number" min="1" name="items[0][quantity_ordered]" required oninput="estimateUnitCost(this)"></div><div class="field"><label>Estimated unit cost (PHP)*</label><input type="number" min="0" step="0.01" name="items[0][unit_cost]" required><small class="muted">Enter quantity to fill the estimate (68% of selling price); adjust to the supplier quote.</small></div></div></div>@error('items')<span class="errors">{{ $message }}</span>@enderror<div style="display:flex;justify-content:flex-end;gap:9px;margin-top:16px"><a class="btn" href="{{ route('purchase-orders.index') }}">Cancel</a><button class="btn primary">Save draft</button></div></form></div></section>
<script>
function estimateUnitCost(quantityInput){
    const row = quantityInput.closest('.item-line');
    const product = row.querySelector('select[name$="[product_id]"]').selectedOptions[0];
    const cost = row.querySelector('input[name$="[unit_cost]"]');

    if (!product || !product.value || Number(quantityInput.value) <= 0) {
        return;
    }

    if (!cost.dataset.manual) {
        const baseUnitCost = Number(product.dataset.unitCost || 0);
        const quantity = Number(quantityInput.value || 0);
        cost.value = (baseUnitCost * quantity).toFixed(2);
    }
}

function productChanged(select){
    const row = select.closest('.item-line');
    const cost = row.querySelector('input[name$="[unit_cost]"]');
    cost.value = '';
    delete cost.dataset.manual;
    estimateUnitCost(row.querySelector('input[name$="[quantity_ordered]"]'));
}

function addLine(){
    const root = document.getElementById('items');
    const i = root.querySelectorAll('.item-line').length;
    const copy = root.querySelector('.item-line').cloneNode(true);
    copy.querySelectorAll('input').forEach(el => { el.value = ''; delete el.dataset.manual; });
    copy.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    copy.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
    });
    root.appendChild(copy);
}

document.getElementById('items').addEventListener('input', event => {
    if (event.target.matches('input[name$="[unit_cost]"]')) {
        event.target.dataset.manual = 'true';
    }
});
</script>
@endsection
