<template>
    <AdminLayout>
        <template #header>Withdrawal Requests</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">All Requests</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Date</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Reseller</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Method & Details</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Amount</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-center">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="wd in withdrawals.data" :key="wd.id" class="border-b hover:bg-gray-50">
                            <td class="p-4 text-sm text-gray-500">
                                {{ new Date(wd.created_at).toLocaleString() }}
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ wd.reseller?.name }}</div>
                                <div class="text-xs text-gray-500">{{ wd.reseller?.reseller_code }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-sm capitalize">{{ wd.payment_method }}</div>
                                <div class="text-xs text-gray-500">{{ wd.payment_details?.account_number }} ({{ wd.payment_details?.account_name }})</div>
                            </td>
                            <td class="p-4 text-right font-medium text-gray-900">
                                ৳{{ Number(wd.amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium uppercase tracking-wide"
                                    :class="{
                                        'bg-amber-100 text-amber-800': wd.status === 'pending',
                                        'bg-blue-100 text-blue-800': wd.status === 'approved',
                                        'bg-emerald-100 text-emerald-800': wd.status === 'paid',
                                        'bg-red-100 text-red-800': wd.status === 'rejected'
                                    }">
                                    {{ wd.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button v-if="wd.status === 'pending'" @click="approve(wd.id)" class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">Approve</button>
                                <button v-if="wd.status === 'pending'" @click="reject(wd.id)" class="text-red-600 hover:text-red-900 text-sm font-medium">Reject</button>
                                <button v-if="wd.status === 'approved'" @click="pay(wd.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Mark Paid</button>
                            </td>
                        </tr>
                        <tr v-if="withdrawals.data.length === 0">
                            <td colspan="6" class="p-6 text-center text-gray-500">No withdrawal requests found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="withdrawals.data.length > 0">
                <Pagination :links="withdrawals.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    withdrawals: Object,
});

const approve = (id) => {
    if (confirm('Approve this request for payment processing?')) {
        router.patch(route('admin.affiliate.withdrawals.approve', id), {}, { preserveScroll: true });
    }
};

const reject = (id) => {
    const reason = prompt('Reason for rejection (funds will be returned to wallet):');
    if (reason) {
        router.patch(route('admin.affiliate.withdrawals.reject', id), { reason }, { preserveScroll: true });
    }
};

const pay = (id) => {
    const note = prompt('Optional: Transaction ID or Note:');
    if (confirm('Confirm funds have been transferred to the reseller?')) {
        router.patch(route('admin.affiliate.withdrawals.pay', id), { admin_note: note }, { preserveScroll: true });
    }
};
</script>
