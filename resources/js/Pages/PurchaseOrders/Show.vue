<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    purchaseOrder: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'Pending': 'bg-amber-100 text-amber-800',
        'Partial': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-emerald-100 text-emerald-800',
        'Cancelled': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout>
        <Head :title="`Purchase Order ${purchaseOrder.po_no}`" />

        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('purchase-orders.index')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-3">
                        Purchase Order Details 
                        <span class="text-sm font-normal text-slate-500">#{{ purchaseOrder.po_no }}</span>
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full" :class="getStatusColor(purchaseOrder.status)">
                        {{ purchaseOrder.status }}
                    </span>
                    <Link v-if="purchaseOrder.status === 'Pending' || purchaseOrder.status === 'Partial'" :href="route('purchases.create', { po_id: purchaseOrder.id })" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 transition ease-in-out duration-150 shadow-sm">
                        Create GRN (Receive Goods)
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">PO Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-8">
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase">Supplier</div>
                            <div class="mt-1 text-sm text-slate-900 font-medium">{{ purchaseOrder.supplier?.name }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase">Date Created</div>
                            <div class="mt-1 text-sm text-slate-900 font-medium">{{ new Date(purchaseOrder.created_at).toLocaleDateString() }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase">Expected Delivery</div>
                            <div class="mt-1 text-sm text-rose-600 font-medium">{{ purchaseOrder.expected_delivery_date ? new Date(purchaseOrder.expected_delivery_date).toLocaleDateString() : 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase">Source Requisition</div>
                            <div class="mt-1 text-sm text-slate-900 font-medium">{{ purchaseOrder.requisition?.requisition_no || 'N/A' }}</div>
                        </div>
                        <div class="col-span-2 md:col-span-4">
                            <div class="text-xs font-semibold text-slate-500 uppercase">Notes</div>
                            <div class="mt-1 text-sm text-slate-700 bg-slate-50 p-3 rounded-lg">{{ purchaseOrder.notes || 'No notes provided.' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                    <div class="p-6 bg-white border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Ordered Items</h3>
                        <span class="text-sm font-bold text-indigo-600">Total: ${{ parseFloat(purchaseOrder.total_amount).toFixed(2) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Item Name</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Ordered Qty</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Received Qty</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Unit Price</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-for="item in purchaseOrder.items" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900">{{ item.item?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ item.ordered_qty }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-bold" :class="item.received_qty >= item.ordered_qty ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50'" style="padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ item.received_qty }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-slate-900">${{ parseFloat(item.unit_price).toFixed(2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-slate-900">${{ parseFloat(item.subtotal).toFixed(2) }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
