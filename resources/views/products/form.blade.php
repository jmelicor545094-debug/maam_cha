@extends('layouts.app')
@section('title', $product->exists ? 'Edit product' : 'Add product')
@section('section', 'Products')
@section('content')
<style>[hidden]{display:none!important}</style>
<div class="page-heading"><div><div class="eyebrow">Inventory master data</div><h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1><div class="muted">Enter the catalog information used for stock monitoring.</div></div><a class="btn" href="{{ route('products.index') }}">← Back to products</a></div>
<section class="panel"><div class="panel-head"><h2>Product details</h2></div><div style="padding:10px 20px 18px"><form method="post" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">@csrf @if($product->exists) @method('PUT') @endif
<div class="form-grid">
    <div class="field"><label for="sku">SKU*</label><div style="display:flex;gap:7px"><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="64" autocomplete="off">@if(!$product->exists)<button class="btn" id="generate-sku" type="button">Generate</button>@endif</div><span id="sku-category-warning" class="errors" role="alert" hidden>Please choose a category first to generate the SKU.</span>@error('sku')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="name">Product name*</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required>@error('name')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="brand">Brand</label><select id="brand" name="brand"><option value="">Select brand (optional)</option>@php($brandOptions = $brands->merge(['Unilab', 'RiteMed', 'Pfizer', 'GSK', 'Bayer', 'Johnson & Johnson', '3M', 'Medline', 'SafeHands', 'Medcare Generic'])->unique()->sort())@foreach($brandOptions as $brand)<option value="{{ $brand }}" @selected(old('brand', $product->brand) === $brand)>{{ $brand }}</option>@endforeach<option value="__other__" @selected(old('brand') === '__other__')>Other</option></select>@error('brand')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field" id="brand-other-field" hidden><label for="brand_other">New brand name*</label><input id="brand_other" name="brand_other" value="{{ old('brand_other') }}" maxlength="100">@error('brand_other')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="unit">Selling Unit*</label><input id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required>@error('unit')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="category_id">Category*</label><select id="category_id" name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="selling_price">Selling price (PHP)*</label><input id="selling_price" name="selling_price" type="number" step="0.01" min="0" required value="{{ old('selling_price', $product->selling_price ?? 0) }}">@error('selling_price')<span class="errors">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="minimum_stock">Minimum stock level*</label><input id="minimum_stock" name="minimum_stock" type="number" min="0" required value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}">@error('minimum_stock')<span class="errors">{{ $message }}</span>@enderror</div>
</div><div style="display:flex;justify-content:flex-end;gap:9px;margin-top:12px"><a class="btn" href="{{ route('products.index') }}">Cancel</a><button class="btn primary" type="submit">{{ $product->exists ? 'Save changes' : 'Create product' }}</button></div></form></div></section>
@if(!$product->exists)
<script>
    const categorySelect = document.getElementById('category_id');
    const generateButton = document.getElementById('generate-sku');
    const categoryWarning = document.getElementById('sku-category-warning');
    const updateGenerateButton = () => {
        if (categorySelect.value) categoryWarning.hidden = true;
    };
    categorySelect.addEventListener('change', updateGenerateButton);
    updateGenerateButton();

    generateButton?.addEventListener('click', async function () {
        if (!categorySelect.value) {
            categoryWarning.hidden = false;
            categorySelect.focus();
            return;
        }
        const button = this;
        button.disabled = true;
        button.textContent = 'Generating...';
        try {
            const response = await fetch(`${@json(route('products.generate-sku'))}?category_id=${encodeURIComponent(categorySelect.value)}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Could not generate a SKU.');
            const result = await response.json();
            document.getElementById('sku').value = result.sku;
        } catch (error) {
            alert(error.message || 'Could not generate a SKU. Please try again.');
        } finally {
            updateGenerateButton();
            button.textContent = 'Generate';
        }
    });
</script>
@endif
<script>
    const brandSelect = document.getElementById('brand');
    const otherBrandField = document.getElementById('brand-other-field');
    const otherBrandInput = document.getElementById('brand_other');
    function toggleOtherBrand() {
        const isOther = brandSelect.value === '__other__';
        otherBrandField.hidden = !isOther;
        otherBrandInput.required = isOther;
    }
    brandSelect.addEventListener('change', toggleOtherBrand);
    toggleOtherBrand();
</script>
@endsection
