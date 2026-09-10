<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dashboard') - MT Billing
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-slate-100 text-slate-800">

@if(session('success'))
    <div data-swal-flash data-swal-type="success" data-swal-message="{{ session('success') }}"></div>
@elseif(session('error'))
    <div data-swal-flash data-swal-type="error" data-swal-message="{{ session('error') }}"></div>
@endif

<div x-data="{ sidebarOpen: false }" class="min-h-screen">

    <!-- Mobile overlay -->
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-40 w-72
               bg-slate-950 text-white
               transform transition-transform duration-300
               lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >

        <div class="flex h-20 items-center border-b border-white/10 px-6">
            <div>
                <div class="text-xl font-black tracking-tight">
                    MT <span class="text-blue-400">Billing</span>
                </div>

                <div class="text-xs text-slate-400">
                    Store Management
                </div>
            </div>
        </div>

        <div class="px-4 py-5">

            <div class="mb-3 px-3 text-[10px] font-bold uppercase
                        tracking-[0.18em] text-slate-500">
                Overview
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="mb-1 flex items-center gap-3 rounded-xl
                      px-4 py-3 text-sm font-medium
                      hover:bg-white/10
                      {{ request()->routeIs('admin.dashboard')
                          ? 'bg-blue-600 text-white'
                          : 'text-slate-300' }}">

                <span>▣</span>
                Dashboard
            </a>


            <div class="mt-6 mb-3 px-3 text-[10px] font-bold uppercase
                        tracking-[0.18em] text-slate-500">
                Sales
            </div>

            <a href="{{ route('admin.orders') }}"
               class="mb-1 flex items-center gap-3 rounded-xl
                      px-4 py-3 text-sm text-slate-300
                      hover:bg-white/10">
                <span>▤</span>
                Orders
            </a>


            <div class="mt-6 mb-3 px-3 text-[10px] font-bold uppercase
                        tracking-[0.18em] text-slate-500">
                Catalog
            </div>

                <a href="{{ route('admin.products.index') }}"
                    class="menu-link {{ request()->routeIs('admin.products.*') ? 'bg-blue-600 text-white' : '' }}">
                <span>◈</span>
                Products
            </a>

                <a href="{{ route('admin.stocks.index') }}"
                    class="menu-link {{ request()->routeIs('admin.stocks.*') ? 'bg-blue-600 text-white' : '' }}">
                <span>◉</span>
                Stock
            </a>


            <div class="mt-6 mb-3 px-3 text-[10px] font-bold uppercase
                        tracking-[0.18em] text-slate-500">
                People
            </div>

                <a href="{{ route('admin.customers.index') }}"
                    class="menu-link {{ request()->routeIs('admin.customers.*') ? 'bg-blue-600 text-white' : '' }}">
                <span>●</span>
                Customers
            </a>

                <a href="{{ route('admin.employees.index') }}"
                    class="menu-link {{ request()->routeIs('admin.employees.*') ? 'bg-blue-600 text-white' : '' }}">
                <span>♙</span>
                Employees
            </a>

                <a href="{{ route('admin.users.index') }}"
                    class="menu-link {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : '' }}">
                <span>♙</span>
                Users
            </a>


            <div class="mt-6 mb-3 px-3 text-[10px] font-bold uppercase
                        tracking-[0.18em] text-slate-500">
                System
            </div>

            <a href="{{ route('admin.mail-logs') }}"
               class="menu-link">
                <span>✉</span>
                Mail Logs
            </a>

        </div>

        <div class="absolute bottom-0 w-full border-t border-white/10 p-4">

            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center
                            rounded-full bg-blue-600 font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>
                    <div class="text-sm font-semibold">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-slate-500">
                        Administrator
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf

                <button
                    class="w-full rounded-xl bg-white/5 px-4 py-2
                           text-sm text-slate-300 hover:bg-red-500/20
                           hover:text-red-300">
                    Sign out
                </button>
            </form>
        </div>

    </aside>


    <!-- Main -->
    <div class="lg:pl-72">

        <header class="sticky top-0 z-20 flex h-20 items-center
                       justify-between border-b bg-white/90 px-6
                       backdrop-blur">

            <button
                @click="sidebarOpen = true"
                class="rounded-lg bg-slate-100 px-3 py-2 lg:hidden">
                ☰
            </button>

            <div>
                <div class="text-sm text-slate-400">
                    Store Billing System
                </div>

                <h1 class="text-xl font-bold">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-3">

                <div class="hidden rounded-xl px-4 py-2 text-sm sm:block {{ $systemHealth['status_class'] }}" title="{{ $systemHealth['queued_jobs'] }} queued jobs, {{ $systemHealth['query_ms'] ?? '—' }} ms query">
                    ● {{ $systemHealth['status'] }} · {{ $systemHealth['query_ms'] ?? '—' }} ms
                </div>

                <div class="h-10 w-10 rounded-xl bg-slate-900
                            flex items-center justify-center
                            font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>

        </header>

        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>