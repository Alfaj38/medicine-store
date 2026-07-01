<template>
    <AdminLayout>
        <template #header>Subscriptions</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Subscription History & Active Plans</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Company</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Plan</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Billing Cycle</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Expires At</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subscription in subscriptions.data" :key="subscription.id" class="border-b hover:bg-gray-50">
                            <td class="p-4 font-medium text-gray-900">
                                {{ subscription.company?.name }}
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm">{{ subscription.package?.name || subscription.package_id }}</span>
                            </td>
                            <td class="p-4 text-sm capitalize">{{ subscription.billing_cycle }}</td>
                            <td class="p-4">
                                <span v-if="subscription.status === 'active'" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium uppercase tracking-wide">Active</span>
                                <span v-else-if="subscription.status === 'trial'" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium uppercase tracking-wide">Trial</span>
                                <span v-else-if="subscription.status === 'expired'" class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium uppercase tracking-wide">Expired</span>
                                <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium uppercase tracking-wide">{{ subscription.status }}</span>
                            </td>
                            <td class="p-4 text-sm">{{ new Date(subscription.expires_at).toLocaleDateString() }}</td>
                            <td class="p-4 text-right font-medium">
                                {{ subscription.currency }} {{ subscription.amount_paid }}
                            </td>
                        </tr>
                        <tr v-if="subscriptions.data.length === 0">
                            <td colspan="6" class="p-6 text-center text-gray-500">No subscriptions found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="subscriptions.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ subscriptions.from }} to {{ subscriptions.to }} of {{ subscriptions.total }} entries
                </div>
                <Pagination :links="subscriptions.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    subscriptions: Object,
});
</script>
