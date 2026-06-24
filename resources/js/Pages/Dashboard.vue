<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TopNavbar from '@/Components/TopNavbar.vue';

const props = defineProps({
    stats: Object,
    recent_sales: Array,
});
</script>

<template>
    <Head title="Dashboard - SaaSMedi" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Analytics Overview</h1>
                <p class="mt-2 text-sm text-slate-500">Monitor your pharmacy's financial health and daily operations.</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                
                <!-- Today Sales -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Today's Sales</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-emerald-600">${{ parseFloat(stats.today_sales || 0).toFixed(2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-emerald-50 px-5 py-3 border-t border-emerald-100 text-sm text-emerald-700 font-medium">
                        Gross amount invoiced today
                    </div>
                </div>

                <!-- Today Collections -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Today's Collections</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-blue-600">${{ parseFloat(stats.today_collections || 0).toFixed(2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 px-5 py-3 border-t border-blue-100 text-sm text-blue-700 font-medium">
                        Cash/Bank received today
                    </div>
                </div>

                <!-- Today Purchases -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Today's Purchases</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-orange-600">${{ parseFloat(stats.today_purchases || 0).toFixed(2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-orange-50 px-5 py-3 border-t border-orange-100 text-sm text-orange-700 font-medium">
                        Stock bought today
                    </div>
                </div>

                <!-- Total Stock Value -->
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Total Stock Valuation</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-indigo-600">${{ parseFloat(stats.stock_value || 0).toFixed(2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-indigo-50 px-5 py-3 border-t border-indigo-100 text-sm text-indigo-700 font-medium">
                        Based on buy prices
                    </div>
                </div>
            </div>

            <!-- Procurement Overview -->
            <div v-if="procurement" class="mb-8">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Procurement & Inventory Alerts</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    
                    <!-- Items Below Reorder -->
                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                        <div class="p-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 truncate">Below Reorder Level</p>
                                <div class="mt-1 text-2xl font-bold" :class="procurement.items_below_reorder > 0 ? 'text-rose-600' : 'text-slate-900'">
                                    {{ procurement.items_below_reorder }} Items
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Requisitions -->
                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                        <div class="p-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 truncate">Pending Requisitions</p>
                                <div class="mt-1 text-2xl font-bold text-slate-900">
                                    {{ procurement.pending_requisitions }}
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Urgent Requisitions -->
                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-rose-200 ring-1 ring-rose-50" :class="{'animate-pulse': procurement.urgent_requisitions > 0}">
                        <div class="p-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 truncate">Urgent Requisitions</p>
                                <div class="mt-1 text-2xl font-bold" :class="procurement.urgent_requisitions > 0 ? 'text-rose-600' : 'text-slate-900'">
                                    {{ procurement.urgent_requisitions }}
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending POs -->
                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                        <div class="p-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500 truncate">Pending Purchase Orders</p>
                                <div class="mt-1 text-2xl font-bold text-slate-900">
                                    {{ procurement.pending_pos }}
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-5 border-b border-slate-200 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Recent Sales Invoices</h3>
                    <Link :href="route('pos.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">View All</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Invoice No</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="sale in recent_sales" :key="sale.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-emerald-600 sm:pl-6">
                                    {{ sale.invoice_no }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    {{ new Date(sale.created_at).toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-900 font-medium">
                                    {{ sale.customer ? sale.customer.name : 'Walk-in Customer' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-slate-900 text-right">
                                    ${{ parseFloat(sale.total_amount).toFixed(2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span v-if="sale.payment_status === 'paid'" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800">Paid</span>
                                    <span v-else-if="sale.payment_status === 'partial'" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-orange-100 text-orange-800">Partial</span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-rose-100 text-rose-800">Unpaid</span>
                                </td>
                            </tr>
                            <tr v-if="recent_sales.length === 0">
                                <td colspan="5" class="py-8 text-center text-slate-500 text-sm">
                                    No sales recorded yet today.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
