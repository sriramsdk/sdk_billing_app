@extends('layouts.admin')
@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('content')<div class="mb-8"><p class="text-sm text-slate-500">People management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Edit Customer</h2></div><form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="max-w-4xl">@csrf @method('PUT') @include('admin.customers.form')</form>@endsection