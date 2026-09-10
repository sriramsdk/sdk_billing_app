@extends('layouts.admin')
@section('title', 'Edit Stock')
@section('page-title', 'Edit Stock')
@section('content')
<div class="mb-8"><p class="text-sm text-slate-500">Inventory management / {{ $stock->product->sku }}</p><h2 class="mt-1 text-3xl font-black text-slate-900">Edit Stock Record</h2></div>
<form method="POST" action="{{ route('admin.stocks.update', $stock) }}" class="max-w-3xl">@csrf @method('PUT') @include('admin.stocks.form')</form>
@endsection