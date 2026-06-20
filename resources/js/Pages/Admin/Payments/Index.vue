<template>
    <AdminLayout>
        <template #header>Payments</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800">All Subscription Payments</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="p-4">Transaction ID</th>
                            <th class="p-4">Tenant</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Method</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-mono text-xs text-slate-500">{{ payment.transaction_id || 'N/A' }}</td>
                            <td class="p-4 font-bold text-indigo-600">{{ payment.company_name }}</td>
                            <td class="p-4 font-bold text-slate-800">৳{{ payment.net_amount }}</td>
                            <td class="p-4 text-slate-600 capitalize">{{ payment.gateway }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold"
                                    :class="payment.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                    {{ payment.status }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-500">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                        </tr>
                        <tr v-if="payments.data.length === 0">
                            <td colspan="6" class="p-8 text-center text-slate-500 font-medium">No payments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';

defineProps({
    payments: Object,
});
</script>
