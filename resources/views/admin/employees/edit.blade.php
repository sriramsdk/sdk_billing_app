@extends('layouts.admin')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('content')<div class="mb-8"><p class="text-sm text-slate-500">People management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Edit Employee</h2></div><form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="max-w-4xl">@csrf @method('PUT') @include('admin.employees.form')</form>@endsection