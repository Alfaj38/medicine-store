<template>
    <AdminLayout>
        <template #header>Platform Dashboard</template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Stat Cards -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Total Companies</div>
                <div class="text-3xl font-bold text-gray-900">{{ stats.total_companies }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Active Companies</div>
                <div class="text-3xl font-bold text-gray-900">{{ stats.active_companies }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Pending Companies</div>
                <div class="text-3xl font-bold text-gray-900">{{ stats.pending_companies }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Total Users</div>
                <div class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Pending Branch Approvals</div>
                <div class="text-3xl font-bold text-gray-900">{{ stats.pending_branches }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="text-sm font-medium text-gray-500 mb-1">Monthly Recurring Revenue</div>
                <div class="text-3xl font-bold text-gray-900">৳{{ stats.mrr }}</div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <p class="text-gray-500">Charts and activity feeds will be implemented here.</p>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from './Layout.vue';

defineProps({
    stats: Object,
});
</script>
