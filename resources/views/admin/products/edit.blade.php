@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="mb-8">
    <p class="text-sm text-slate-500">Catalog management / {{ $product->sku }}</p>
    <h2 class="mt-1 text-3xl font-black text-slate-900">Edit Product</h2>
</div>
<form method="POST" action="{{ route('admin.products.update', $product) }}" class="max-w-4xl">
    @csrf
    @method('PUT')
    @include('admin.products.form')
</form>
@endsection