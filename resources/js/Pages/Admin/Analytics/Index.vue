<template>
    <AdminLayout>
        <template #header>Platform Analytics</template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Revenue Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Monthly Recurring Revenue</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Platform-wide MRR over the last 7 months</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl">
                        💰
                    </div>
                </div>
                
                <apexchart type="area" height="350" :options="revenueOptions" :series="revenueData.series"></apexchart>
            </div>

            <!-- Growth Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Tenant Growth</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Total active paying tenants on the platform</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                        📈
                    </div>
                </div>
                
                <apexchart type="bar" height="350" :options="growthOptions" :series="growthData.series"></apexchart>
            </div>

            <!-- Feature Adoption Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Feature Adoption</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Usage of core modules across all tenants</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl">
                        🎯
                    </div>
                </div>
                
                <apexchart type="radar" height="350" :options="adoptionOptions" :series="adoptionData.series"></apexchart>
            </div>

            <!-- Churn Risk Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Tenant Churn Risk</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Predicted probability of churn within 30 days</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-xl">
                        🚨
                    </div>
                </div>
                
                <apexchart type="donut" height="350" :options="churnOptions" :series="churnData.series"></apexchart>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import AdminLayout from '../Layout.vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    revenueData: Object,
    growthData: Object,
});

// Configure ApexCharts
const apexchart = VueApexCharts;

const revenueOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, sans-serif'
    },
    colors: ['#10b981'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 100]
        }
    },
    xaxis: {
        categories: props.revenueData.categories,
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            formatter: (value) => { return "৳" + value.toLocaleString() }
        }
    },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
}));

const growthOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, sans-serif'
    },
    colors: ['#3b82f6'],
    plotOptions: {
        bar: {
            borderRadius: 6,
            columnWidth: '45%',
        }
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.growthData.categories,
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
}));

const adoptionData = {
    series: [{
        name: 'Adoption Rate',
        data: [80, 50, 30, 40, 100, 20],
    }]
};

const adoptionOptions = computed(() => ({
    chart: { type: 'radar', toolbar: { show: false }, fontFamily: 'Instrument Sans, sans-serif' },
    labels: ['Sales', 'Purchases', 'Returns', 'Reports', 'Inventory', 'Coupons'],
    colors: ['#8b5cf6'],
    yaxis: { show: false },
}));

const churnData = {
    series: [85, 10, 5]
};

const churnOptions = computed(() => ({
    chart: { type: 'donut', fontFamily: 'Instrument Sans, sans-serif' },
    labels: ['Safe', 'At Risk', 'High Risk'],
    colors: ['#10b981', '#f59e0b', '#ef4444'],
    plotOptions: { donut: { size: '70%' } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom' }
}));
</script>
