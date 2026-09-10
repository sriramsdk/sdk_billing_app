<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Employee Dashboard') - MT Billing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-employee-app class="bg-slate-100 text-slate-800">
    @if(session('success'))<div data-swal-flash data-swal-type="success" data-swal-message="{{ session('success') }}"></div>@endif
    @if(session('error'))<div data-swal-flash data-swal-type="error" data-swal-message="{{ session('error') }}"></div>@endif
    <div id="employee-page-loader" class="pointer-events-none fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/70 p-6 backdrop-blur-sm" aria-live="polite" aria-busy="true">
        <div class="w-full max-w-sm rounded-3xl border border-white/10 bg-slate-900 p-8 text-center text-white shadow-2xl shadow-slate-950/40">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-500/15 ring-1 ring-blue-400/30">
                <div class="h-9 w-9 animate-spin rounded-full border-4 border-blue-300/25 border-t-blue-400"></div>
            </div>
            <h2 class="mt-6 text-lg font-black">Working on it</h2>
            <p id="employee-page-loader-message" class="mt-2 text-sm text-slate-400">Please wait while we complete your request.</p>
            <div class="mt-6 flex justify-center gap-1.5"><span class="h-1.5 w-8 animate-pulse rounded-full bg-blue-400"></span><span class="h-1.5 w-8 animate-pulse rounded-full bg-blue-400 [animation-delay:150ms]"></span><span class="h-1.5 w-8 animate-pulse rounded-full bg-blue-400 [animation-delay:300ms]"></span></div>
        </div>
    </div>
    <div x-data="{ profileOpen: false }" class="min-h-screen">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
            <div class="mx-auto flex min-h-20 max-w-screen-2xl flex-wrap items-center gap-4 px-6 py-3">
                <a href="{{ route('employee.billing') }}" class="mr-3 shrink-0">
                    <div class="text-xl font-black text-slate-900">MT <span class="text-blue-600">Billing</span></div>
                    <div class="text-xs text-slate-400">Employee Desk</div>
                </a>
                <nav class="order-3 flex w-full items-center gap-1 overflow-x-auto border-t border-slate-100 pt-3 md:order-2 md:w-auto md:flex-1 md:border-0 md:pt-0">
                    <a href="{{ route('employee.dashboard') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('employee.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                    <a href="{{ route('employee.billing') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('employee.billing') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">New Bill</a>
                    <a href="{{ route('employee.orders') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('employee.orders*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">My Orders</a>
                </nav>
                <div data-system-health data-health-endpoint="{{ route('employee.health') }}" data-health-refresh="{{ config('app.health_refresh_seconds') * 1000 }}" class="hidden rounded-xl px-3 py-2 text-xs font-semibold sm:block {{ $systemHealth['status_class'] }}" title="{{ $systemHealth['queued_jobs'] }} queued jobs, {{ $systemHealth['failed_jobs'] }} historical failed jobs, {{ $systemHealth['query_ms'] ?? '—' }} ms query">
                    <span data-health-status>{{ $systemHealth['status'] }}</span> · <span data-health-query>{{ $systemHealth['query_ms'] ?? '—' }}</span> ms · <span data-health-queued>{{ $systemHealth['queued_jobs'] }}</span> queued
                </div>
                <div class="relative order-2 ml-auto md:order-3">
                    <button type="button" @click="profileOpen = !profileOpen" class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-left hover:bg-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden sm:block"><span class="block text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span><span class="block text-xs text-slate-500">Employee</span></span>
                        <span class="text-slate-400">⌄</span>
                    </button>
                    <div x-show="profileOpen" @click.outside="profileOpen = false" x-cloak class="absolute right-0 top-12 z-30 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                        <div class="border-b border-slate-100 px-3 py-2"><div class="text-xs uppercase tracking-wider text-slate-400">Signed in as</div><div class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->email }}</div></div>
                        <form method="POST" action="{{ route('employee.logout') }}" class="mt-1">@csrf<button class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Sign out</button></form>
                    </div>
                </div>
            </div>
        </header>
        <main class="mx-auto max-w-screen-2xl p-6"><div class="mb-6"><div class="text-sm text-slate-400">Employee Workspace</div><h1 class="text-xl font-bold">@yield('page-title', 'Dashboard')</h1></div>@yield('content')</main>
    </div>
</body>
</html>