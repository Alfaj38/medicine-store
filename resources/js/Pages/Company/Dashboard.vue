<template>
    <AppLayout title="Company Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Company Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Branches</div>
                        <div class="text-3xl font-bold text-gray-900">{{ stats.total_branches }}</div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-emerald-500">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Users</div>
                        <div class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 mb-1">Monthly Sales</div>
                        <div class="text-3xl font-bold text-gray-900">৳{{ stats.monthly_sales }}</div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-amber-500">
                        <div class="text-sm font-medium text-gray-500 mb-1">Today's Sales</div>
                        <div class="text-3xl font-bold text-gray-900">৳{{ stats.today_sales }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Overview</h3>
                    <p class="text-gray-500">Additional company metrics and charts will be displayed here.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    stats: Object,
});
</script>
