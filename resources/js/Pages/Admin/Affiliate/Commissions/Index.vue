<template>
    <AdminLayout>
        <template #header>Global Commissions</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">All Commissions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Date</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Reseller</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Company</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Event</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Payment</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Commission</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="comm in commissions.data" :key="comm.id" class="border-b hover:bg-gray-50">
                            <td class="p-4 text-sm text-gray-500">
                                {{ new Date(comm.created_at).toLocaleString() }}
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ comm.reseller?.name }}</div>
                                <div class="text-xs text-gray-500">{{ comm.reseller?.reseller_code }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ comm.company?.name }}</div>
                            </td>
                            <td class="p-4 text-sm capitalize">
                                {{ comm.event_type }}
                            </td>
                            <td class="p-4 text-right text-sm">
                                ৳{{ Number(comm.payment_amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-right font-medium text-emerald-600">
                                ৳{{ Number(comm.commission_amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium uppercase tracking-wide"
                                    :class="{
                                        'bg-amber-100 text-amber-800': comm.status === 'pending',
                                        'bg-emerald-100 text-emerald-800': comm.status === 'paid',
                                        'bg-red-100 text-red-800': comm.status === 'reversed'
                                    }">
                                    {{ comm.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="commissions.data.length === 0">
                            <td colspan="7" class="p-6 text-center text-gray-500">No commissions found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="commissions.data.length > 0">
                <Pagination :links="commissions.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    commissions: Object,
});
</script>
