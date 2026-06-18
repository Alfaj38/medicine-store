<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    inventory: Object,
    stats: Object,
    now: String,
});

const getExpiryStatusClass = (dateString) => {
    if (!dateString) return 'bg-slate-100 text-slate-800';
    
    const expiry = new Date(dateString);
    const today = new Date(props.now);
    
    // Time difference in months
    const diffTime = expiry - today;
    const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30);
    
    if (diffMonths < 0) {
        return 'bg-rose-100 text-rose-800 font-bold'; // Expired
    } else if (diffMonths <= 3) {
        return 'bg-orange-100 text-orange-800 font-semibold'; // Expires within 3 months
    } else if (diffMonths <= 6) {
        return 'bg-amber-100 text-amber-800'; // Expires within 6 months
    } else {
        return 'bg-emerald-100 text-emerald-800'; // Safe
    }
};

const getExpiryLabel = (dateString) => {
    if (!dateString) return 'No Date';
    
    const expiry = new Date(dateString);
    const today = new Date(props.now);
    
    const diffTime = expiry - today;
    const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30);
    
    if (diffMonths < 0) return 'Expired';
    if (diffMonths <= 3) return '< 3 Months';
    if (diffMonths <= 6) return '< 6 Months';
    return 'Safe';
};
</script>

<template>
    <Head title="Expiry Dashboard - MediSaaS" />

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
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Batch & Expiry Management</h1>
                <p class="mt-2 text-sm text-slate-500">Monitor medicine batches to prevent selling expired products and reduce wastage.</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Already Expired</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-rose-600">{{ stats.already_expired }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-rose-50 px-5 py-3 border-t border-rose-100 text-sm">
                        <a href="#" class="font-medium text-rose-700 hover:text-rose-900">Review immediately &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Expiring in &lt; 3 Months</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-orange-600">{{ stats.expiring_in_3_months }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-orange-50 px-5 py-3 border-t border-orange-100 text-sm">
                        <a href="#" class="font-medium text-orange-700 hover:text-orange-900">Plan promotional sales &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Expiring in 3-6 Months</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-amber-600">{{ stats.expiring_in_6_months - stats.expiring_in_3_months }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-amber-50 px-5 py-3 border-t border-amber-100 text-sm">
                        <span class="font-medium text-amber-700">Monitor closely</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-5 border-b border-slate-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Inventory Expiry Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Medicine</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Batch No.</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Quantity Left</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Expiry Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="item in inventory.data" :key="item.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="font-semibold text-slate-900">{{ item.medicine?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ item.medicine?.generic_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                                    {{ item.batch_no || 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-900">
                                    {{ item.quantity }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                                    {{ item.expiry_date }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium" :class="getExpiryStatusClass(item.expiry_date)">
                                        {{ getExpiryLabel(item.expiry_date) }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="inventory.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <p class="text-lg font-medium text-slate-900">No batch data found</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
