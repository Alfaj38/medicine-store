<script setup>
import { Head, Link, Deferred } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, defineAsyncComponent } from 'vue';

const VueApexCharts = defineAsyncComponent(() => import('vue3-apexcharts'));

const props = defineProps({
    stats: Object,
    procurement: Object,
    recent_sales: Array,
    charts: Object,
});

// Helper to safely convert incoming data to an array before mapping
const safeArray = (data) => {
    if (!data) return [];
    if (Array.isArray(data)) return data;
    if (typeof data === 'object') return Object.values(data);
    return [];
};

// Chart configuration using computed to ensure completely new objects are passed on update, avoiding ApexCharts mutation bugs
const chartOptionsSalesOverview = computed(() => {
    const dataArray = safeArray(props.charts?.sales_overview);
    return {
        chart: { type: 'line', toolbar: { show: false }, height: 280 },
        stroke: { width: [0, 2], curve: 'straight' },
        colors: ['#34d399', '#059669'],
        fill: { type: 'solid', opacity: [1, 1] },
        xaxis: { 
            type: 'category',
            categories: dataArray.length ? dataArray.map(s => String(s.day || '-')) : ['-']
        },
        yaxis: [
            {
                title: { text: 'Sales' },
                min: 0, 
                labels: { formatter: (value) => value >= 1000 ? (value/1000).toFixed(1) + 'K' : Number(value).toFixed(0) }
            },
            {
                opposite: true,
                title: { text: 'Transactions' },
                min: 0,
            }
        ],
        markers: { size: [0, 4] },
        legend: { show: true, position: 'top', horizontalAlign: 'left' }
    };
});

const seriesSalesOverview = computed(() => {
    const dataArray = safeArray(props.charts?.sales_overview);
    return [
        { name: 'Sales', type: 'column', data: dataArray.map(s => Number(s.amount) || 0) },
        // Simulate transactions for now
        { name: 'Transactions', type: 'line', data: dataArray.map(() => 0) }
    ];
});

const defaultColors = ['#3b82f6', '#60a5fa', '#f59e0b', '#fbbf24', '#10b981', '#ef4444', '#8b5cf6'];

const chartOptionsCategory = computed(() => {
    const dataArray = safeArray(props.charts?.sales_by_category);
    const catTotal = dataArray.reduce((sum, c) => sum + Number(c.total_sales || 0), 0);
    return {
        chart: { type: 'donut', height: 240 },
        labels: dataArray.length ? dataArray.map(c => String(c.category_name || 'Unknown')) : ['No Data'],
        colors: defaultColors.slice(0, Math.max(1, dataArray.length)),
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: { 
                        show: true, name: { show: true }, 
                        value: { show: true, formatter: (val) => '৳' + Number(val).toLocaleString() }, 
                        total: { show: true, label: 'Total Sales', formatter: () => '৳' + catTotal.toLocaleString() } 
                    }
                }
            }
        },
        legend: { show: false }
    };
});

const seriesCategory = computed(() => {
    const dataArray = safeArray(props.charts?.sales_by_category);
    return dataArray.length ? dataArray.map(c => Number(c.total_sales || 0)) : [0];
});

const chartOptionsPayment = computed(() => {
    const dataArray = safeArray(props.charts?.payment_methods);
    return {
        chart: { type: 'donut', height: 180 },
        labels: dataArray.length ? dataArray.map(p => String(p.payment_method || 'Unknown')) : ['No Data'],
        colors: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
        dataLabels: { enabled: false },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: false } } } },
        legend: { show: false }
    };
});

const seriesPayment = computed(() => {
    const dataArray = safeArray(props.charts?.payment_methods);
    return dataArray.length ? dataArray.map(p => Number(p.total_paid || 0)) : [0];
});

const formatCurrency = (val) => {
    return '৳' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>

<template>
    <Head title="Dashboard - SaaSMedi" />

    <AppLayout>
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 bg-slate-50 min-h-screen">
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ $page.props.auth.user.name || 'Store Manager' }}! 👋</h1>
                    <p class="text-sm text-slate-500 mt-1">Here's what's happening with your pharmacy today.</p>
                </div>
                <div>
                    <button class="flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        May 12 - May 18, 2024
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <!-- Today Sales -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 flex items-center gap-1">Today's Sales <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></p>
                            <Deferred data="stats">
                                <template #fallback><div class="h-8 w-24 bg-slate-200 animate-pulse rounded mt-1"></div></template>
                                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ formatCurrency(props.stats?.today_sales || 0) }}</h3>
                            </Deferred>
                            <p class="text-xs font-medium text-emerald-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                18.6% vs yesterday
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 h-10 w-full relative">
                        <svg class="w-full h-full text-emerald-400 opacity-50" preserveAspectRatio="none" viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 15 Q 10 5, 20 10 T 40 10 T 60 5 T 80 15 T 100 5"></path></svg>
                    </div>
                </div>

                <!-- Today Collections -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 flex items-center gap-1">Today's Collections <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></p>
                            <Deferred data="stats">
                                <template #fallback><div class="h-8 w-24 bg-slate-200 animate-pulse rounded mt-1"></div></template>
                                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ formatCurrency(props.stats?.today_collections || 0) }}</h3>
                            </Deferred>
                            <p class="text-xs font-medium text-blue-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                12.4% vs yesterday
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 h-10 w-full relative">
                        <svg class="w-full h-full text-blue-400 opacity-50" preserveAspectRatio="none" viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 10 Q 15 15, 25 5 T 50 15 T 75 5 T 100 10"></path></svg>
                    </div>
                </div>

                <!-- Today Purchases -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 flex items-center gap-1">Today's Purchases <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></p>
                            <Deferred data="stats">
                                <template #fallback><div class="h-8 w-24 bg-slate-200 animate-pulse rounded mt-1"></div></template>
                                <h3 class="text-2xl font-bold text-orange-500 mt-1">{{ formatCurrency(props.stats?.today_purchases || 0) }}</h3>
                            </Deferred>
                            <p class="text-xs font-medium text-orange-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                8.7% vs yesterday
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 h-10 w-full relative">
                        <svg class="w-full h-full text-orange-400 opacity-50" preserveAspectRatio="none" viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 5 Q 20 15, 40 5 T 70 15 T 100 10"></path></svg>
                    </div>
                </div>

                <!-- Total Stock Valuation -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 flex items-center gap-1">Total Stock Valuation <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></p>
                            <Deferred data="stats">
                                <template #fallback><div class="h-8 w-24 bg-slate-200 animate-pulse rounded mt-1"></div></template>
                                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ formatCurrency(props.stats?.stock_value || 0) }}</h3>
                            </Deferred>
                            <p class="text-xs font-medium text-emerald-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                5.3% vs last week
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 h-10 w-full relative">
                        <svg class="w-full h-full text-indigo-400 opacity-50" preserveAspectRatio="none" viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 15 Q 25 5, 50 15 T 80 5 T 100 10"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Second Row: Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Sales Overview (Combo Chart) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            Sales Overview
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </h3>
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-600 text-xs py-1.5 pl-3 pr-8 rounded-lg focus:outline-none focus:border-slate-300">
                                <option>This Week</option>
                                <option>Last Week</option>
                                <option>This Month</option>
                            </select>
                            <svg class="w-3 h-3 text-slate-400 absolute right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <Deferred data="charts">
                        <template #fallback><div class="h-[280px] flex items-center justify-center text-slate-400 animate-pulse bg-slate-50 rounded-xl">Loading chart data...</div></template>
                        <VueApexCharts v-if="safeArray(props.charts?.sales_overview).length" type="line" height="280" :options="chartOptionsSalesOverview" :series="seriesSalesOverview"></VueApexCharts>
                        <div v-else class="h-[280px] flex items-center justify-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">No sales data available.</div>
                    </Deferred>
                </div>

                <!-- Sales by Category (Donut Chart) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800">Sales by Category</h3>
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-600 text-xs py-1.5 pl-3 pr-8 rounded-lg focus:outline-none focus:border-slate-300">
                                <option>This Week</option>
                            </select>
                            <svg class="w-3 h-3 text-slate-400 absolute right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <Deferred data="charts">
                        <template #fallback><div class="h-[240px] flex items-center justify-center text-slate-400 animate-pulse bg-slate-50 rounded-xl mt-4">Loading data...</div></template>
                        <div class="flex justify-center -ml-4" v-if="safeArray(props.charts?.sales_by_category).length">
                            <VueApexCharts type="donut" height="240" :options="chartOptionsCategory" :series="seriesCategory"></VueApexCharts>
                        </div>
                        <div v-else class="h-[240px] flex items-center justify-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 mt-4">No category data.</div>
                    </Deferred>
                    
                    <div class="mt-2 space-y-3 pl-2">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Medicines
                            </div>
                            <span class="text-slate-500">$5,245.30 <span class="text-slate-400 ml-1">(59.9%)</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span> OTC Products
                            </div>
                            <span class="text-slate-500">$1,854.40 <span class="text-slate-400 ml-1">(21.2%)</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Health Care
                            </div>
                            <span class="text-slate-500">$1,125.30 <span class="text-slate-400 ml-1">(12.8%)</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Personal Care
                            </div>
                            <span class="text-slate-500">$529.30 <span class="text-slate-400 ml-1">(6.1%)</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row: Tables & Widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left tables column -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Top Selling Products -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                        <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Top Selling Products</h3>
                            <div class="relative">
                                <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-600 text-xs py-1.5 pl-3 pr-8 rounded-lg focus:outline-none focus:border-slate-300">
                                    <option>This Week</option>
                                </select>
                                <svg class="w-3 h-3 text-slate-400 absolute right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <div class="flex-1 p-0 overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/50">
                                    <tr>
                                        <th class="px-5 py-3 font-medium">Product</th>
                                        <th class="px-5 py-3 font-medium text-center">Qty Sold</th>
                                        <th class="px-5 py-3 font-medium text-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-slate-600">
                                    <tr v-for="(product, index) in safeArray(props.charts?.top_products)" :key="index">
                                        <td class="px-5 py-3 font-medium text-slate-800">{{ product.name }}</td>
                                        <td class="px-5 py-3 text-center">{{ product.qty_sold }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-emerald-500" :style="`width: ${Math.max(10, (product.revenue / Math.max(...(safeArray(props.charts?.top_products).map(p => p.revenue) || [1]))) * 100)}%`"></div>
                                                </div>
                                                {{ formatCurrency(product.revenue) }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="!safeArray(props.charts?.top_products).length">
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-500 font-medium">No sales data for this period.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-slate-50 text-center">
                            <Link href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">View All Products</Link>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                        <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Low Stock Alert</h3>
                        </div>
                        <div class="flex-1 p-0 overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/50">
                                    <tr>
                                        <th class="px-5 py-3 font-medium">Product</th>
                                        <th class="px-5 py-3 font-medium text-center">Current Stock</th>
                                        <th class="px-5 py-3 font-medium text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-slate-600">
                                    <tr v-for="(item, index) in safeArray(props.charts?.low_stock)" :key="index">
                                        <td class="px-5 py-3 font-medium text-slate-800">{{ item.name }}</td>
                                        <td class="px-5 py-3 text-center">{{ item.stock }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold"
                                                  :class="item.status === 'Critical' ? 'bg-rose-50 text-rose-600' : 'bg-orange-50 text-orange-600'">
                                                {{ item.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!safeArray(props.charts?.low_stock).length">
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-500 font-medium">No low stock items.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-slate-50 text-center">
                            <Link href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">View All Inventory</Link>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Right column (Widgets) -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Payment Methods -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-slate-800">Payment Methods <span class="text-slate-400 font-normal text-xs ml-1">(This Week)</span></h3>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-1/3">
                                <Deferred data="charts">
                                    <template #fallback><div class="h-[150px] flex items-center justify-center text-slate-400 animate-pulse bg-slate-50 rounded-xl">...</div></template>
                                    <VueApexCharts v-if="safeArray(props.charts?.payment_methods).length" type="donut" height="150" :options="chartOptionsPayment" :series="seriesPayment"></VueApexCharts>
                                    <div v-else class="h-[150px] flex items-center justify-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs text-center p-2">No payment data.</div>
                                </Deferred>
                            </div>
                            <div class="w-2/3 pl-4 space-y-3">
                                <div v-for="(method, index) in safeArray(props.charts?.payment_methods)" :key="index" class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'][index % 4] }"></span> 
                                        <span class="capitalize">{{ method.payment_method }}</span>
                                    </div>
                                    <span class="text-slate-500">{{ formatCurrency(method.total_paid) }} 
                                        <span class="text-slate-400 ml-1">({{ ((method.total_paid / Math.max(...safeArray(props.charts.payment_methods).map(p => p.total_paid))) * 100).toFixed(1) }}%)</span>
                                    </span>
                                </div>
                                <div v-if="!safeArray(props.charts?.payment_methods).length" class="text-xs text-slate-500 text-center py-4">No payment data available.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col flex-1">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-slate-800">Recent Activities</h3>
                            <Link href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">View All</Link>
                        </div>
                        
                        <div class="space-y-6">
                            <div v-for="(sale, index) in safeArray(props.recent_sales)" :key="index" class="flex gap-4">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-medium text-slate-800">New sale created</p>
                                        <span class="text-[10px] text-slate-400">{{ new Date(sale.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">INV-{{ sale.id }} &bull; {{ formatCurrency(sale.total_amount) }}</p>
                                </div>
                            </div>
                            <div v-if="!safeArray(props.recent_sales).length" class="text-xs text-slate-500 text-center py-4">No recent activity.</div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </main>
    </AppLayout>
</template>
