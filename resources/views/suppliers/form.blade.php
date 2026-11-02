@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit supplier' : 'Add supplier')
@section('section', 'Suppliers')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Partner records</div><h1>{{ $supplier->exists ? 'Edit supplier' : 'Add supplier' }}</h1></div><a class="btn" href="{{ route('suppliers.index') }}">← Back</a></div>
<section class="panel"><div class="panel-head"><h2>Supplier details</h2></div><div style="padding:10px 20px 18px"><form method="post" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">@csrf @if($supplier->exists) @method('PUT') @endif<div class="form-grid">
@foreach(['company_name' => 'Company name', 'contact_person' => 'Contact person', 'phone' => 'Phone', 'email' => 'Email', 'city' => 'City', 'country' => 'Country'] as $field => $label)<div class="field"><label for="{{ $field }}">{{ $label }} @if($field === 'company_name')* @endif</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $supplier->$field) }}" {{ $field === 'company_name' ? 'required' : '' }}>@error($field)<span class="errors">{{ $message }}</span>@enderror</div>@endforeach
<div class="field" style="grid-column:1/-1"><label for="address">Address</label><input id="address" name="address" value="{{ old('address', $supplier->address) }}">@error('address')<span class="errors">{{ $message }}</span>@enderror</div></div><div style="display:flex;justify-content:flex-end;gap:9px;margin-top:12px"><a class="btn" href="{{ route('suppliers.index') }}">Cancel</a><button class="btn primary">Save supplier</button></div></form></div></section>
@endsection
