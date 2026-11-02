@extends('layouts.app')
@section('title', 'Profile')
@section('section', 'Profile')
@section('content')
@php($tab = request('tab') === 'password' ? 'password' : 'about')
<style>
    .profile-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px}
    .profile-heading h1{font-size:23px;margin:0 0 5px;color:#243a42}
    .profile-crumb{font-size:11px;color:#819196}
    .profile-card{background:#fff;border:1px solid #e2e9e9;border-radius:6px;overflow:hidden;margin-bottom:14px}
    .profile-identity{display:flex;align-items:center;gap:14px;padding:20px}
    .profile-avatar{display:grid;place-items:center;width:62px;height:62px;flex:0 0 62px;border-radius:50%;background:#c9eee2;color:#176b5d;font-size:23px;font-weight:700}
    .profile-name{font-size:15px;font-weight:700;color:#263d44}
    .profile-email{font-size:11px;color:#7c8d92;margin-top:4px}
    .profile-role{display:inline-flex;margin-top:7px;padding:3px 7px;border-radius:3px;background:#eef5f2;color:#47786c;font-size:10px;font-weight:600}
    .profile-tabs{display:flex;gap:5px;padding:8px 18px;border-top:1px solid #edf0f0}
    .profile-tab{padding:7px 11px;border-radius:3px;color:#667b80;font-size:11px}
    .profile-tab.active{background:#14776b;color:white}
    .profile-section{padding:16px 19px}
    .profile-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:13px}
    .profile-section-head h2{font-size:14px;margin:0;color:#2b4349}
    .profile-facts{display:grid;grid-template-columns:150px minmax(0,1fr);gap:0 12px;max-width:700px}
    .profile-facts dt,.profile-facts dd{margin:0;padding:7px 0;border-bottom:1px solid #f0f2f2;font-size:11px}
    .profile-facts dt{color:#7a8b90}.profile-facts dd{color:#344d54;font-weight:500}
    .profile-edit{margin-top:14px}
    .profile-edit summary{list-style:none;cursor:pointer}
    .profile-edit summary::-webkit-details-marker{display:none}
    .profile-edit-form{margin-top:14px;padding-top:5px;border-top:1px solid #edf0f0}
    .profile-password{padding:16px 19px}
    .profile-password h2{font-size:14px;margin:0 0 10px}
    @media(max-width:600px){.profile-heading{align-items:center}.profile-identity{padding:16px}.profile-facts{grid-template-columns:110px minmax(0,1fr)}.profile-section,.profile-password{padding:14px}}
</style>
<div class="profile-heading"><div><h1>Profile</h1><div class="profile-crumb">Dashboard / Profile</div></div><a class="btn" href="{{ route('permission-requests.mine') }}">My requests</a></div>
<section class="profile-card">
    <div class="profile-identity"><div class="profile-avatar">{{ strtoupper(substr($profile->name, 0, 1)) }}</div><div><div class="profile-name">{{ $profile->name }}</div><div class="profile-email">{{ $profile->email }}</div><span class="profile-role">{{ ucfirst($profile->role) }}</span></div></div>
    <nav class="profile-tabs" aria-label="Profile sections"><a class="profile-tab {{ $tab === 'about' ? 'active' : '' }}" href="{{ route('profile.edit', ['tab' => 'about']) }}">About</a><a class="profile-tab {{ $tab === 'password' ? 'active' : '' }}" href="{{ route('profile.edit', ['tab' => 'password']) }}">Password</a></nav>
</section>
@if($tab === 'about')
    @if($errors->any())<div class="flash" style="background:#fce8e7;color:#a6423c;border-color:#f3d3d0">{{ $errors->first() }}</div>@endif
    <section class="profile-card profile-section">
        <div class="profile-section-head"><div><h2>Personal Details</h2>@if($profile->role !== 'admin')<div class="muted" style="margin-top:4px">Changes are sent to an administrator for approval.</div>@endif</div><details class="profile-edit"><summary class="btn">Edit details</summary>
            <form class="profile-edit-form" method="post" action="{{ route('profile.update', ['tab' => 'about']) }}">@csrf @method('PUT')
                <div class="form-grid">
                    @foreach(['name' => 'Name', 'email' => 'Email ID', 'contact_number' => 'Contact number', 'birthday' => 'Birthday', 'street' => 'Street', 'city' => 'City', 'province' => 'Province', 'zip_code' => 'ZIP code'] as $field => $label)
                        <div class="field"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $field === 'birthday' ? $profile->birthday?->format('Y-m-d') : $profile->$field) }}" @if(in_array($field,['name','email'])) required @endif @if($field === 'birthday') type="date" max="{{ today()->subDay()->format('Y-m-d') }}" @endif>@error($field)<span class="errors">{{ $message }}</span>@enderror</div>
                    @endforeach
                    <div class="field"><label for="sex">Sex</label><select id="sex" name="sex"><option value="">Select</option><option value="female" @selected(old('sex',$profile->sex)==='female')>Female</option><option value="male" @selected(old('sex',$profile->sex)==='male')>Male</option><option value="prefer_not_to_say" @selected(old('sex',$profile->sex)==='prefer_not_to_say')>Prefer not to say</option></select>@error('sex')<span class="errors">{{ $message }}</span>@enderror</div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:10px"><button class="btn primary" type="submit">{{ $profile->role === 'admin' ? 'Save profile' : 'Request changes' }}</button></div>
            </form>
        </details></div>
        <dl class="profile-facts"><dt>Name</dt><dd>{{ $profile->name }}</dd><dt>Email ID</dt><dd>{{ $profile->email }}</dd><dt>User Role</dt><dd>{{ ucfirst($profile->role) }}</dd><dt>Contact number</dt><dd>{{ $profile->contact_number ?: 'Not provided' }}</dd><dt>Birthday</dt><dd>{{ $profile->birthday?->format('d M, Y') ?? 'Not provided' }}</dd><dt>Address</dt><dd>{{ collect([$profile->street, $profile->city, $profile->province, $profile->zip_code])->filter()->join(', ') ?: 'Not provided' }}</dd></dl>
    </section>
    <section class="profile-card profile-section"><div class="profile-section-head"><h2>Deactivate account</h2></div><p class="muted">Your account will be archived and you will be signed out. An administrator can restore it later.</p>@error('deactivate')<div class="errors">{{ $message }}</div>@enderror<form method="post" action="{{ route('profile.deactivate') }}" onsubmit="return confirm('Deactivate this account and sign out?')">@csrf<div style="display:flex;gap:10px;align-items:end;flex-wrap:wrap"><div class="field" style="margin:0;min-width:240px"><label for="deactivate_password">Confirm with current password</label><input id="deactivate_password" type="password" name="current_password" required autocomplete="current-password"></div><button class="btn danger" type="submit">Deactivate account</button></div></form></section>
@else
    @if($errors->any())<div class="flash" style="background:#fce8e7;color:#a6423c;border-color:#f3d3d0">{{ $errors->first() }}</div>@endif
    <section class="profile-card profile-password"><h2>Change password</h2><p class="muted">Choose a strong password you do not use elsewhere.</p><form method="post" action="{{ route('profile.password', ['tab' => 'password']) }}">@csrf @method('PUT')<div class="form-grid"><div class="field"><label for="current_password">Current password</label><input id="current_password" type="password" name="current_password" required autocomplete="current-password"></div><div class="field"><label for="password">New password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"></div><div class="field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div></div>@error('current_password')<span class="errors">{{ $message }}</span>@enderror @error('password')<span class="errors">{{ $message }}</span>@enderror<div style="display:flex;justify-content:flex-end;margin-top:12px"><button class="btn primary" type="submit">Update password</button></div></form></section>
@endif
@endsection
