@extends('layouts.admin')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
<div class="mb-8">
    <p class="text-sm text-slate-500">Catalog management</p>
    <h2 class="mt-1 text-3xl font-black text-slate-900">Add Product</h2>
</div>
<form method="POST" action="{{ route('admin.products.store') }}" class="max-w-4xl">
    @csrf
    @include('admin.products.form')
</form>
@endsection