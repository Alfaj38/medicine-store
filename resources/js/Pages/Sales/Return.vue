<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    sale: Object,
});

const form = useForm({
    sale_id: props.sale.id,
    notes: '',
    items: props.sale.items.map(item => ({
        sale_item_id: item.id,
        medicine: item.medicine,
        batch_no: item.batch_no,
        unit_price: item.unit_price,
        sold_quantity: item.quantity,
        returned_quantity: item.returned_quantity,
        return_quantity: 0,
        selected: false,
    })),
});

const availableItems = computed(() => {
    return form.items.filter(item => (item.sold_quantity - item.returned_quantity) > 0);
});

const totalRefundAmount = computed(() => {
    return form.items.reduce((sum, item) => {
        if (item.selected && item.return_quantity > 0) {
            return sum + (item.return_quantity * item.unit_price);
        }
        return sum;
    }, 0);
});

const validateQuantities = () => {
    form.items.forEach(item => {
        if (item.selected) {
            const max = item.sold_quantity - item.returned_quantity;
            if (item.return_quantity > max) item.return_quantity = max;
            if (item.return_quantity < 1) item.return_quantity = 1;
        }
    });
};

const submitReturn = () => {
    // Only send selected items with return quantity > 0
    const returnItems = form.items.filter(item => item.selected && item.return_quantity > 0).map(item => ({
        sale_item_id: item.sale_item_id,
        return_quantity: item.return_quantity,
    }));

    if (returnItems.length === 0) {
        alert('Please select at least one item to return.');
        return;
    }

    form.transform((data) => ({
        ...data,
        items: returnItems,
    })).post(route('sale-returns.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Process Return - MediSaaS" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-500 shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <span class="font-bold text-xl tracking-tight text-slate-800">MediSaaS</span>
                        </div>
                        <div class="hidden sm:flex space-x-8 h-full">
                            <Link :href="route('dashboard')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url === '/dashboard' ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Dashboard
                            </Link>
                            <Link :href="route('medicines.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/medicines') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Master Data
                            </Link>
                            <Link :href="route('suppliers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/suppliers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Suppliers
                            </Link>
                            <Link :href="route('purchases.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/purchases') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Purchases
                            </Link>
                            <Link :href="route('sales.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/sales') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Sales
                            </Link>
                            <Link :href="route('customers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/customers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Customers
                            </Link>
                            <Link :href="route('reports.expiry')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Reports
                            </Link>
                            <Link v-if="$page.props.auth.user.is_company_owner" :href="route('company.settings.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/company') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Settings
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('pos.index')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all">
                            Open POS Terminal
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Process Return for {{ sale.invoice_no }}</h1>
                    <p class="text-slate-500 text-sm mt-1">Select items and quantities to refund to the customer.</p>
                </div>
                <Link :href="route('sales.index')" class="text-slate-500 hover:text-slate-700 text-sm font-medium">← Back to Sales</Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-semibold text-slate-800">Return Details</h3>
                </div>
                
                <div class="p-6">
                    <div v-if="availableItems.length === 0" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">All Items Returned</h3>
                        <p class="mt-1 text-sm text-slate-500">Every item from this invoice has already been fully returned.</p>
                    </div>

                    <form v-else @submit.prevent="submitReturn" class="space-y-6">
                        <div class="overflow-x-auto border border-slate-200 rounded-lg">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 w-12 text-center"></th>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Medicine</th>
                                        <th scope="col" class="py-3 px-4 text-center text-xs font-semibold text-slate-500">Available Qty</th>
                                        <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500">Unit Price</th>
                                        <th scope="col" class="py-3 px-4 text-center text-xs font-semibold text-slate-500">Return Qty</th>
                                        <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500">Refund</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr v-for="(item, index) in availableItems" :key="index" :class="{'bg-rose-50/30': item.selected}">
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" v-model="item.selected" @change="item.selected && item.return_quantity === 0 ? item.return_quantity = 1 : null" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 h-4 w-4">
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <div class="font-medium text-slate-900">{{ item.medicine.name }}</div>
                                            <div class="text-xs text-slate-500">Batch: {{ item.batch_no }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-center font-medium text-slate-700">
                                            {{ item.sold_quantity - item.returned_quantity }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-right text-slate-700">
                                            ${{ parseFloat(item.unit_price).toFixed(2) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="number" 
                                                v-model.number="item.return_quantity" 
                                                @input="validateQuantities"
                                                :disabled="!item.selected"
                                                :max="item.sold_quantity - item.returned_quantity" 
                                                min="1"
                                                class="w-20 rounded-md border-slate-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500 disabled:bg-slate-100 disabled:text-slate-400 text-center mx-auto block">
                                        </td>
                                        <td class="py-3 px-4 text-sm text-right font-bold" :class="item.selected ? 'text-rose-600' : 'text-slate-400'">
                                            ${{ item.selected ? (item.return_quantity * item.unit_price).toFixed(2) : '0.00' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Return Notes / Reason</label>
                            <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Optional: Reason for return..."></textarea>
                        </div>

                        <div class="bg-rose-50 p-4 rounded-lg flex items-center justify-between border border-rose-100">
                            <div>
                                <h4 class="text-rose-800 font-semibold">Total Refund Amount</h4>
                                <p class="text-rose-600 text-sm">This amount will be deducted from cash drawer or credited to customer.</p>
                            </div>
                            <div class="text-3xl font-bold text-rose-700">
                                ${{ totalRefundAmount.toFixed(2) }}
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('sales.index')" class="px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing || totalRefundAmount <= 0" class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                Confirm Return & Restock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>
