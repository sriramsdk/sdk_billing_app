@extends('layouts.admin')
@section('title', 'Add User')
@section('page-title', 'Add User')
@section('content')<div class="mb-8"><p class="text-sm text-slate-500">Access management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Add User</h2></div><form method="POST" action="{{ route('admin.users.store') }}" class="max-w-4xl">@csrf @include('admin.users.form')</form>@endsection