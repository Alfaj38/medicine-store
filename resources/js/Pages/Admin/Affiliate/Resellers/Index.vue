<template>
    <AdminLayout>
        <template #header>Resellers</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Active Resellers</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Reseller</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Reseller Code</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Rate</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Wallet Balance</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-center">Codes Generated</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-center">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="reseller in resellers.data" :key="reseller.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ reseller.name }}</div>
                                <div class="text-sm text-gray-500">{{ reseller.email }}</div>
                            </td>
                            <td class="p-4 font-mono text-sm">{{ reseller.reseller_code }}</td>
                            <td class="p-4 font-medium text-emerald-600">{{ Number(reseller.commission_rate) }}%</td>
                            <td class="p-4 text-right font-medium">৳{{ Number(reseller.wallet_balance).toLocaleString() }}</td>
                            <td class="p-4 text-center">{{ reseller.promo_codes_count }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium uppercase tracking-wide"
                                    :class="reseller.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'">
                                    {{ reseller.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <Link :href="route('admin.affiliate.resellers.show', reseller.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</Link>
                            </td>
                        </tr>
                        <tr v-if="resellers.data.length === 0">
                            <td colspan="7" class="p-6 text-center text-gray-500">No resellers found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="resellers.data.length > 0">
                <Pagination :links="resellers.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    resellers: Object,
});
</script>
