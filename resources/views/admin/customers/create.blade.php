@extends('layouts.admin')
@section('title', 'Add Customer')
@section('page-title', 'Add Customer')
@section('content')<div class="mb-8"><p class="text-sm text-slate-500">People management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Add Customer</h2></div><form method="POST" action="{{ route('admin.customers.store') }}" class="max-w-4xl">@csrf @include('admin.customers.form')</form>@endsection