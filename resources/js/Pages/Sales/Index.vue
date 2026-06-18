<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    sales: Object,
    filters: Object,
    totals: Object,
});

const dateFilter = ref(props.filters.start_date ? 'custom' : 'today');
const startDate = ref(props.filters.start_date || new Date().toISOString().slice(0, 10));
const endDate = ref(props.filters.end_date || new Date().toISOString().slice(0, 10));

const updateFilters = () => {
    let params = {};
    const today = new Date();
    
    if (dateFilter.value === 'today') {
        params.start_date = today.toISOString().slice(0, 10);
    } else if (dateFilter.value === 'yesterday') {
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        params.start_date = yesterday.toISOString().slice(0, 10);
        params.end_date = yesterday.toISOString().slice(0, 10);
    } else if (dateFilter.value === 'this_week') {
        const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
        params.start_date = firstDay.toISOString().slice(0, 10);
    } else if (dateFilter.value === 'this_month') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        params.start_date = firstDay.toISOString().slice(0, 10);
    } else if (dateFilter.value === 'custom') {
        params.start_date = startDate.value;
        if (endDate.value) params.end_date = endDate.value;
    }

    router.get(route('sales.index'), params, { preserveState: true, preserveScroll: true });
};

const handleDateChange = debounce(() => {
    if (dateFilter.value === 'custom') updateFilters();
}, 500);

const setFilter = (filter) => {
    dateFilter.value = filter;
    updateFilters();
};
</script>

<template>
    <Head title="Sales History - MediSaaS" />

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
                        <Link :href="route('pos.index')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Open POS Terminal
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Sales History</h1>
                    <p class="mt-2 text-sm text-slate-500">Monitor your daily and weekly sales performance.</p>
                </div>
            </div>
                
                <!-- Filters -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap gap-2 items-center justify-between">
                    <div class="flex gap-2">
                        <button @click="setFilter('today')" :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', dateFilter === 'today' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100']">Today</button>
                        <button @click="setFilter('yesterday')" :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', dateFilter === 'yesterday' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100']">Yesterday</button>
                        <button @click="setFilter('this_week')" :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', dateFilter === 'this_week' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100']">This Week</button>
                        <button @click="setFilter('this_month')" :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', dateFilter === 'this_month' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100']">This Month</button>
                        <button @click="setFilter('custom')" :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', dateFilter === 'custom' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100']">Custom</button>
                    </div>
                    
                    <div v-if="dateFilter === 'custom'" class="flex items-center gap-2">
                        <input v-model="startDate" @change="handleDateChange" type="date" class="border-slate-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="text-slate-400">to</span>
                        <input v-model="endDate" @change="handleDateChange" type="date" class="border-slate-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-500">Total Revenue</div>
                            <div class="text-2xl font-bold text-slate-900">${{ parseFloat(totals.revenue).toFixed(2) }}</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-500">Total Discounts</div>
                            <div class="text-2xl font-bold text-slate-900">${{ parseFloat(totals.discount).toFixed(2) }}</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-500">Cash Collected</div>
                            <div class="text-2xl font-bold text-slate-900">${{ parseFloat(totals.cash_collected).toFixed(2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Invoice No</th>
                                    <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Date & Time</th>
                                    <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Customer</th>
                                    <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Amount</th>
                                    <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-slate-500">Status</th>
                                    <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="sale in sales.data" :key="sale.id" class="hover:bg-slate-50">
                                    <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ sale.invoice_no }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-sm text-slate-500">{{ new Date(sale.sale_date).toLocaleString() }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-sm text-slate-900">
                                        {{ sale.customer ? sale.customer.name : 'Walk-in Customer' }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-sm font-bold text-emerald-600">${{ parseFloat(sale.total_amount).toFixed(2) }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-sm">
                                        <span v-if="sale.payment_status === 'paid'" class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md text-xs font-medium">Paid</span>
                                        <span v-else-if="sale.payment_status === 'partial'" class="px-2 py-1 bg-amber-100 text-amber-700 rounded-md text-xs font-medium">Partial</span>
                                        <span v-else class="px-2 py-1 bg-rose-100 text-rose-700 rounded-md text-xs font-medium">Unpaid</span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('sales.show', sale.id)" class="text-indigo-600 hover:text-indigo-900">View</Link>
                                        <Link :href="route('sale-returns.create', { sale_id: sale.id })" class="text-rose-600 hover:text-rose-900">Return</Link>
                                    </td>
                                </tr>
                                <tr v-if="sales.data.length === 0">
                                    <td colspan="6" class="py-8 text-center text-slate-500">No sales found for this period.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div v-if="sales.links.length > 3" class="bg-white px-4 py-3 border-t border-slate-200 sm:px-6 flex justify-center">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link v-for="(link, i) in sales.links" :key="i" :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === sales.links.length - 1 ? 'rounded-r-md' : '',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]" />
                        </nav>
                    </div>
                </div>

        </main>
    </div>
</template>
