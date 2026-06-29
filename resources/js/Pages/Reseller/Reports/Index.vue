<script setup>
import { Head, router } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    filters: Object,
    kpis: Object,
    chartData: Object,
    campaigns: Array
});

const chartOptions = {
    chart: {
        type: 'area',
        fontFamily: 'inherit',
        toolbar: { show: false },
        zoom: { enabled: false },
        sparkline: { enabled: false }
    },
    colors: ['#00b67a'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100]
        }
    },
    dataLabels: { enabled: false },
    stroke: { width: 3, curve: 'smooth' },
    xaxis: {
        categories: props.chartData.labels,
        labels: {
            style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: {
            style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 },
            formatter: (value) => `৳${value.toLocaleString()}`
        }
    },
    grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
        yaxis: { lines: { show: true } }
    },
    tooltip: {
        theme: 'light',
        y: { formatter: (value) => `৳${value.toLocaleString()}` }
    }
};

const updatePeriod = (event) => {
    router.get(route('reseller.reports'), { period: event.target.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Performance Reports - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Performance Reports</h2>
                <p class="text-slate-500 font-medium text-sm mt-1">Deep dive into your referral analytics and conversion metrics.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <select :value="filters.period" @change="updatePeriod" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a] outline-none cursor-pointer">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">This Year</option>
                </select>
                <button class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </button>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <h3 class="text-slate-500 text-[13px] font-bold uppercase tracking-wider mb-2 relative z-10">Total Revenue Generated</h3>
                <div class="text-3xl font-black text-slate-800 relative z-10">৳{{ Number(kpis.total_revenue).toLocaleString() }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <h3 class="text-slate-500 text-[13px] font-bold uppercase tracking-wider mb-2 relative z-10">Successful Referrals</h3>
                <div class="text-3xl font-black text-slate-800 relative z-10">{{ kpis.total_referrals }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <h3 class="text-slate-500 text-[13px] font-bold uppercase tracking-wider mb-2 relative z-10">Avg. Comm. per Referral</h3>
                <div class="text-3xl font-black text-slate-800 relative z-10">৳{{ Number(kpis.avg_commission).toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Chart Area -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-[15px] text-slate-800">Earnings Trend</h3>
                </div>
                <div class="p-6">
                    <VueApexCharts type="area" height="300" :options="chartOptions" :series="chartData.series" />
                </div>
            </div>

            <!-- Campaign Breakdown -->
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-[15px] text-slate-800">Top Performing Campaigns</h3>
                </div>
                <div class="p-0 overflow-y-auto max-h-[348px]">
                    <div v-if="campaigns.length === 0" class="p-8 text-center text-slate-500 text-sm font-medium">
                        No active campaigns generated revenue in this period.
                    </div>
                    <div v-else class="divide-y divide-slate-100">
                        <div v-for="campaign in campaigns" :key="campaign.code" class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-800 text-sm">{{ campaign.label }}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">Code: {{ campaign.code }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-[#00b67a] text-sm">৳{{ Number(campaign.earnings).toLocaleString() }}</div>
                                <div class="text-[11px] font-bold text-slate-500 mt-0.5">{{ campaign.referrals }} conversions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
