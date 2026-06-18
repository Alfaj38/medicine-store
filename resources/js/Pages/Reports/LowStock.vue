<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    medicines: Object,
});
</script>

<template>
    <Head title="Low Stock Alerts - MediSaaS" />

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
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Low Stock Alerts</h1>
                    <p class="mt-2 text-sm text-slate-500">Medicines that have fallen below their configured reorder level. Plan your purchases accordingly.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('purchases.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Create Purchase Order
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Medicine</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Total Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Reorder Level</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="med in medicines.data" :key="med.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="font-semibold text-slate-900">{{ med.name }}</div>
                                    <div class="text-xs text-slate-500">{{ med.generic_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <span class="text-lg font-bold" :class="(med.total_stock || 0) === 0 ? 'text-red-600' : 'text-orange-600'">
                                        {{ med.total_stock || 0 }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center text-sm font-medium text-slate-700">
                                    {{ med.reorder_level || 10 }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center text-sm">
                                    <span v-if="(med.total_stock || 0) === 0" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        Out of Stock
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                        Low Stock
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="medicines.data.length === 0">
                                <td colspan="4" class="py-12 text-center text-slate-500">
                                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-lg font-medium text-slate-900">All stocks are healthy!</p>
                                    <p class="mt-1">No medicines are currently below their reorder level.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
