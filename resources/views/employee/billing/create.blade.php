@extends('layouts.employee')

@section('title', 'Create Bill')
@section('page-title', 'Create New Bill')

@section('content')

@if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700 shadow-sm" role="alert">
        <div class="font-bold">Bill could not be created</div>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('employee.billing.store') }}"
    x-data="billingForm()"
    data-billing-form
    class="space-y-6"
    @submit.prevent="submitBill()"
>
    @csrf

    <!-- Header -->
    {{-- <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

        <div>
            <p class="text-sm text-slate-500">
                Billing Desk
            </p>

            <h2 class="text-3xl font-black text-slate-900">
                Create New Bill
            </h2>
        </div>

        <div class="flex items-center gap-3">

            <span class="rounded-full bg-emerald-50 px-4 py-2
                         text-xs font-bold text-emerald-700">
                ● Ready to Bill
            </span>

        </div>

    </div> --}}


    <!-- Customer -->
    <div class="rounded-2xl bg-white p-6
                shadow-sm ring-1 ring-slate-200">

        <div class="mb-5 flex items-center justify-between">

            <div>
                <h3 class="font-bold text-slate-900">
                    Customer
                </h3>

                <p class="text-xs text-slate-500">
                    Select an existing customer or create a walk-in bill.
                </p>
            </div>

            <button
                type="button"
                @click="selectWalkIn(); customerMode = 'new'"
                class="rounded-lg bg-blue-50 px-4 py-2
                       text-sm font-semibold text-blue-600">

                + New Customer

            </button>

        </div>


        <div class="grid gap-5 md:grid-cols-3">

            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold">
                    Customer
                </label>

                <div class="relative" @click.outside="customerOpen = false">
                    <input type="hidden" name="customer_id" :value="customer">
                    <input type="search" x-model="customerSearch" @focus="customerOpen = true" @input="customerOpen = true" placeholder="Search all customers by name, phone, or email" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    <button type="button" x-show="customer" x-cloak @click="selectWalkIn()" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 hover:text-slate-700">Clear</button>
                    <div x-show="customerOpen" x-cloak class="absolute left-0 right-0 top-full z-30 mt-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                        <button type="button" @click="selectWalkIn()" class="flex w-full items-center justify-between border-b border-slate-100 px-4 py-3 text-left hover:bg-blue-50"><span><span class="block font-semibold text-slate-800">Walk-in Customer</span><span class="block text-xs text-slate-500">No customer record</span></span><span class="text-xs font-bold text-blue-600">Default</span></button>
                        <div class="max-h-72 overflow-y-auto">
                            <template x-for="item in filteredCustomers()" :key="item.id">
                                <button type="button" @click="selectCustomer(item)" class="flex w-full items-center justify-between px-4 py-3 text-left hover:bg-slate-50"><span><span class="block font-semibold text-slate-800" x-text="item.name"></span><span class="block text-xs text-slate-500" x-text="item.email || item.phone || 'No contact details'"></span></span><span x-show="item.attended_orders_count > 0" class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700" x-text="item.attended_orders_count + ' attended'"></span></button>
                            </template>
                            <div x-show="filteredCustomers().length === 0" class="px-4 py-5 text-sm text-slate-500">No customers match this search.</div>
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500"><span x-show="!customer">Walk-in Customer selected.</span><span x-show="customer" x-cloak><span class="font-semibold text-slate-700" x-text="customerLabel"></span> selected.</span> Showing 10 customers initially; search includes every active customer.</p>

                <div x-show="customerMode === 'new'" class="mt-4 grid gap-4 md:grid-cols-2">
                    <input type="text" name="customer_name" x-model="customerName" placeholder="New customer name" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <input type="email" name="customer_email" x-model="customerEmail" placeholder="New customer email" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                </div>

                <input type="hidden" name="customer_mode" :value="customerMode">

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">
                    Bill Date
                </label>

                <input
                    type="date"
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3"
                >

            </div>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">

        <!-- Products -->
        <div class="rounded-2xl bg-white shadow-sm
                    ring-1 ring-slate-200">

            <div class="border-b px-6 py-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="font-bold text-slate-900">
                            Products
                        </h3>

                        <p class="text-xs text-slate-500">
                            Add products to this invoice.
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-100 px-3 py-2
                                text-xs font-bold text-slate-600">

                        <span x-text="items.length"></span>
                        items

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[750px]">

                    <thead class="bg-slate-50 text-left text-xs
                                  uppercase tracking-wider
                                  text-slate-500">

                    <tr>

                        <th class="px-6 py-4">
                            Product
                        </th>

                        <th class="px-4 py-4">
                            Stock
                        </th>

                        <th class="px-4 py-4">
                            Qty
                        </th>

                        <th class="px-4 py-4">
                            Price
                        </th>

                        <th class="px-4 py-4">
                            Tax
                        </th>

                        <th class="px-4 py-4 text-right">
                            Total
                        </th>

                        <th></th>

                    </tr>

                    </thead>

                    <tbody class="divide-y">

                    <template x-for="(item, index) in items"
                              :key="item.key">

                        <tr>

                            <td class="px-6 py-4">

                                <select
                                    x-model="item.product_id"
                                    @change="productChanged(index)"
                                     :name="`items[${index}][product_id]`"
                                     class="w-full rounded-lg
                                         border border-slate-300
                                         px-3 py-2 text-sm">
                                    <option value="">
                                        Select product
                                    </option>

                                    @foreach($products as $product)

                                        <option
                                            value="{{ $product->id }}"
                                            data-price="{{ $product->selling_price }}"
                                            data-stock="{{ $product->stock?->quantity ?? 0 }}"
                                            data-tax="{{ $product->tax_rate }}"
                                            data-threshold="{{ $product->low_stock_threshold }}"
                                        >
                                            {{ $product->name }}
                                            — ₹{{ number_format($product->selling_price, 2) }}
                                        </option>

                                    @endforeach

                                </select>

                            </td>


                            <td class="px-4 py-4">

                                <span
                                    class="rounded-full bg-slate-100
                                           px-2.5 py-1 text-xs font-semibold"
                                    x-text="item.stock">
                                </span>

                            </td>


                            <td class="px-4 py-4">

                                <input
                                    type="number"
                                    min="1"
                                    x-model.number="item.qty"
                                    :name="`items[${index}][quantity]`"
                                    @input="calculate()"
                                    class="w-20 rounded-lg
                                           border border-slate-300
                                           px-3 py-2"
                                >

                            </td>


                            <td class="px-4 py-4 font-medium">

                                ₹<span x-text="item.price.toFixed(2)"></span>

                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                <span x-text="item.tax_rate.toFixed(2) + '%' "></span>
                                <div class="mt-1 text-xs text-slate-400">₹<span x-text="taxTotal(item).toFixed(2)"></span></div>

                            </td>


                            <td class="px-4 py-4 text-right font-bold">

                                ₹<span x-text="lineTotal(item).toFixed(2)"></span>

                            </td>


                            <td class="px-4 py-4">

                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="rounded-lg p-2 text-red-500
                                           hover:bg-red-50">

                                    ×

                                </button>

                            </td>

                        </tr>

                    </template>

                    </tbody>

                </table>

            </div>


            <div class="border-t bg-slate-50 px-6 py-5">

                <button
                    type="button"
                    @click="addItem()"
                    class="rounded-xl bg-slate-900 px-5 py-3
                           text-sm font-bold text-white
                           hover:bg-slate-800">

                    + Add Product

                </button>

            </div>

        </div>


        <!-- Summary -->
        <div class="space-y-5">

            <!-- Low Stock -->
            <div class="rounded-2xl border border-amber-200
                        bg-amber-50 p-5">

                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-lg">⚠</div>
                    <div class="min-w-0 flex-1">

                        <h3 class="font-bold text-amber-900">
                            Low Stock Alert
                        </h3>

                        <p class="mt-1 text-xs leading-5
                                  text-amber-700">

                            Products reaching their stock threshold
                            will appear here.

                        </p>

                        <div x-show="selectedLowStockItems().length" x-cloak class="mt-4 border-t border-amber-200 pt-4 text-xs text-amber-800">
                            <div class="mb-2 font-bold">Selected products needing attention</div>
                            <ul class="space-y-2">
                                <template x-for="item in selectedLowStockItems()" :key="item.key">
                                    <li class="flex items-center justify-between gap-3 rounded-lg bg-white/70 px-3 py-2">
                                        <span class="min-w-0 truncate font-semibold" x-text="item.name"></span>
                                        <span class="shrink-0 rounded-full bg-amber-100 px-2 py-1 font-bold" x-text="item.stock + ' left'"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                            </div>

                </div>

            </div>


            <!-- Summary -->
            <div class="rounded-2xl bg-slate-950 p-6 text-white">

                <h3 class="text-lg font-bold">
                    Payment Summary
                </h3>

                <div class="mt-6 space-y-4">

                    <div class="flex justify-between text-sm
                                text-slate-400">

                        <span>Subtotal</span>

                        <span>
                            ₹<span x-text="subtotal.toFixed(2)"></span>
                        </span>

                    </div>

                    <div class="flex justify-between text-sm text-slate-400">
                        <span>Taxable items</span>
                        <span x-text="items.length"></span>
                    </div>

                    <div class="flex justify-between text-sm
                                text-slate-400">

                        <span>Tax</span>

                        <span>
                            ₹<span x-text="tax.toFixed(2)"></span>
                        </span>

                    </div>

                    <div class="border-t border-white/10 pt-4">

                        <div class="flex justify-between">

                            <span class="font-bold">
                                Grand Total
                            </span>

                            <span class="text-2xl font-black">
                                ₹<span x-text="grandTotal.toFixed(2)"></span>
                            </span>

                        </div>

                    </div>

                </div>


                <div class="mt-6">

                    <label class="mb-2 block text-sm font-semibold">
                        Amount Given
                    </label>

                    <input
                        type="number"
                        name="amount_paid"
                        min="0"
                        x-model.number="amountPaid"
                        @input="calculate()"
                        placeholder="₹0.00"
                        class="w-full rounded-xl border-0
                               bg-white/10 px-4 py-3 text-white
                               placeholder:text-slate-500
                               focus:ring-2 focus:ring-blue-500"
                    >

                </div>


                <div class="mt-5 rounded-xl bg-white/5 p-4">

                    <div class="flex justify-between">

                        <span class="text-sm text-slate-400">
                            Balance to Return
                        </span>

                        <span
                            class="font-bold text-emerald-400"
                            x-text="'₹' + balance.toFixed(2)">
                        </span>

                    </div>

                </div>


                <button
                    type="submit"
                    class="mt-6 w-full rounded-xl
                           bg-blue-600 py-4 text-sm font-black
                           shadow-lg shadow-blue-600/20
                           hover:bg-blue-500">

                    Generate Bill

                </button>

            </div>

        </div>

    </div>

</form>

<script>

function billingForm() {

    return {

        customer: '',
        customerSearch: '',
        customerLabel: 'Walk-in Customer',
        customerOpen: false,
        customers: @js($customers->map(fn ($customer) => [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'attended_orders_count' => $customer->attended_orders_count,
        ])->values()),
        customerMode: 'walk-in',
        customerName: '',
        customerEmail: '',

        items: [],

        amountPaid: 0,

        subtotal: 0,

        tax: 0,

        grandTotal: 0,

        balance: 0,

        filteredCustomers() {
            const search = this.customerSearch.trim().toLowerCase();
            const matches = search
                ? this.customers.filter((item) => [item.name, item.email, item.phone].filter(Boolean).some((value) => value.toLowerCase().includes(search)))
                : this.customers;

            return matches.slice(0, search ? matches.length : 10);
        },

        selectWalkIn() {
            this.customer = '';
            this.customerLabel = 'Walk-in Customer';
            this.customerSearch = '';
            this.customerMode = 'walk-in';
            this.customerOpen = false;
        },

        selectCustomer(item) {
            this.customer = item.id;
            this.customerLabel = item.name;
            this.customerSearch = item.name;
            this.customerMode = 'existing';
            this.customerOpen = false;
        },

        addItem() {

                this.items.push({
                key: Date.now() + Math.random(),
                product_id: '',
                qty: 1,
                price: 0,
                stock: 0,
                tax_rate: 0,
                threshold: 0,
                name: ''
            });

        },

        removeItem(index) {

            this.items.splice(index, 1);

            this.calculate();

        },

        productChanged(index) {

            const item = this.items[index];

            const select = document.querySelectorAll(
                'tbody tr'
            )[index]?.querySelector('select');

            if (!select) {
                return;
            }

            const option =
                select.options[select.selectedIndex];

            item.price =
                parseFloat(option.dataset.price || 0);

            item.stock =
                parseInt(option.dataset.stock || 0);

            item.tax_rate =
                parseFloat(option.dataset.tax || 0);

            item.threshold =
                parseInt(option.dataset.threshold || 0);

            item.name = option.textContent.trim().split('—')[0].trim();

            this.calculate();

        },

        lineTotal(item) {

            return item.price * item.qty;

        },

        taxTotal(item) {

            return this.lineTotal(item) * (item.tax_rate / 100);

        },

        calculate() {

            this.subtotal = this.items.reduce(
                (total, item) =>
                    total + this.lineTotal(item),
                0
            );

            this.tax = this.items.reduce(
                (total, item) => total + this.lineTotal(item) * (item.tax_rate / 100),
                0
            );

            this.grandTotal =
                this.subtotal + this.tax;

            this.balance =
                Math.max(
                    this.amountPaid - this.grandTotal,
                    0
                );

        },

        selectedLowStockItems() {
            return this.items.filter((item) => item.product_id && item.stock <= item.threshold);
        },

        submitBill() {

            if (this.items.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Add a product', text: 'Please add at least one product.' });
                return;
            }

            for (const item of this.items) {

                if (!item.product_id) {
                    Swal.fire({ icon: 'warning', title: 'Select a product', text: 'Please select a product for every bill line.' });
                    return;
                }

                if (item.qty <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Invalid quantity', text: 'Quantity must be greater than zero.' });
                    return;
                }

                if (item.qty > item.stock) {
                    Swal.fire({ icon: 'warning', title: 'Stock unavailable', text: `Only ${item.stock} units are available.` });
                    return;
                }
            }

            if (this.amountPaid < this.grandTotal) {
                Swal.fire({ icon: 'warning', title: 'Insufficient payment', text: 'Amount received is less than the grand total.' });
                return;
            }

            if (this.customerMode === 'new' && !this.customerEmail) {
                Swal.fire({ icon: 'warning', title: 'Email required', text: 'Enter the new customer email.' });
                return;
            }

            window.showEmployeeLoader?.('Creating bill...');
            HTMLFormElement.prototype.submit.call(this.$el);
        }

    }

}

</script>

@endsection