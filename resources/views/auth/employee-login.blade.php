<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Employee Login - MT Billing</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950">

<div class="min-h-screen lg:grid lg:grid-cols-2">

    <!-- Branding -->
    <div class="relative hidden overflow-hidden lg:flex
                bg-gradient-to-br from-slate-950 via-blue-950
                to-indigo-950 p-12 text-white">

        <div class="relative z-10 flex flex-col justify-between">

            <div>
                <div class="text-3xl font-black">
                    MT <span class="text-blue-400">Billing</span>
                </div>

                <div class="mt-2 text-sm text-slate-400">
                    Smart Store Billing & Operations
                </div>
            </div>

            <div class="max-w-xl">

                <div class="mb-6 inline-flex rounded-full
                            border border-blue-400/30
                            bg-blue-400/10 px-4 py-2
                            text-sm text-blue-300">
                    Employee Portal
                </div>

                <h1 class="text-5xl font-black leading-tight">
                    Control your entire
                    <span class="text-blue-400">
                        store operation
                    </span>
                    from one place.
                </h1>

                <p class="mt-6 text-lg leading-8 text-slate-400">
                    Manage employees, products, inventory, customers,
                    orders, invoices and audit activity.
                </p>

            </div>

            <div class="text-xs text-slate-500">
                MT Billing • Secure Administration
            </div>

        </div>

        <div class="absolute -right-24 -top-24 h-96 w-96
                    rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="absolute -bottom-32 -left-20 h-96 w-96
                    rounded-full bg-indigo-500/20 blur-3xl"></div>

    </div>


    <!-- Login -->
    <div class="flex items-center justify-center
                bg-slate-100 px-6 py-12">

        <div class="w-full max-w-md">

            <div class="mb-8 lg:hidden">
                <div class="text-3xl font-black text-slate-900">
                    MT <span class="text-blue-600">Billing</span>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-8 shadow-xl
                        shadow-slate-900/5 ring-1 ring-slate-200">

                <div class="mb-8">

                    <div class="mb-4 flex h-14 w-14 items-center
                                justify-center rounded-2xl
                                bg-blue-600 text-xl font-black
                                text-white shadow-lg
                                shadow-blue-600/20">
                        MT
                    </div>

                    <h2 class="text-2xl font-black text-slate-900">
                        Employee sign in
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Create bills and manage your sales orders.
                    </p>

                </div>


                @if($errors->any())

                    <div class="mb-5 rounded-xl bg-red-50 p-4
                                text-sm text-red-700">

                        {{ $errors->first() }}

                    </div>

                @endif


                <form method="POST"
                      action="{{ route('employee.login.submit') }}"
                      class="space-y-5">

                    @csrf

                    <div>

                        <label class="mb-2 block text-sm font-semibold">
                            Email address
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@mtbilling.test"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 outline-none
                                   transition focus:border-blue-500
                                   focus:ring-4 focus:ring-blue-500/10"
                            required
                            autofocus
                        >

                    </div>


                    <div>

                        <div class="mb-2 flex justify-between">

                            <label class="text-sm font-semibold">
                                Password
                            </label>

                        </div>

                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 outline-none
                                   transition focus:border-blue-500
                                   focus:ring-4 focus:ring-blue-500/10"
                            required
                        >

                    </div>


                    <label class="flex items-center gap-2 text-sm
                                  text-slate-500">

                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-blue-600"
                        >

                        Remember me

                    </label>


                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600
                               py-3.5 text-sm font-bold text-white
                               shadow-lg shadow-blue-600/20
                               transition hover:bg-blue-700">

                        Enter Billing Desk

                    </button>

                </form>


                <div class="mt-6 border-t pt-6 text-center">

                    <a href="{{ route('admin.login') }}"
                       class="text-sm font-semibold text-blue-600
                              hover:text-blue-700">

                        ← Administrator Login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>