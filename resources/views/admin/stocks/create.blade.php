@extends('layouts.admin')
@section('title', 'Add Stock')
@section('page-title', 'Add Stock')
@section('content')
<div class="mb-8"><p class="text-sm text-slate-500">Inventory management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Add Stock Record</h2></div>
@if($products->isEmpty())<div class="max-w-3xl rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">Every product already has a stock record. Add a product or edit existing stock instead.</div>@else<form method="POST" action="{{ route('admin.stocks.store') }}" class="max-w-3xl">@csrf @include('admin.stocks.form')</form>@endif
@endsection