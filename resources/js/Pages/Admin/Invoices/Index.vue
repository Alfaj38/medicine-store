<template>
    <AdminLayout>
        <template #header>Invoices</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800">All Invoices</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="p-4">Invoice #</th>
                            <th class="p-4">Tenant</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-bold text-indigo-600">{{ invoice.invoice_number }}</td>
                            <td class="p-4 font-medium text-slate-800">{{ invoice.company?.name }}</td>
                            <td class="p-4 font-bold text-slate-800">৳{{ invoice.net_amount }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold"
                                    :class="invoice.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                    {{ invoice.status }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-500">{{ new Date(invoice.created_at).toLocaleDateString() }}</td>
                        </tr>
                        <tr v-if="invoices.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No invoices found.</td>
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
    invoices: Object,
});
</script>
