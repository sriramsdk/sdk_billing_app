@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('content')<div class="mb-8"><p class="text-sm text-slate-500">Access management</p><h2 class="mt-1 text-3xl font-black text-slate-900">Edit User</h2></div><form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-4xl">@csrf @method('PUT') @include('admin.users.form')</form>@endsection