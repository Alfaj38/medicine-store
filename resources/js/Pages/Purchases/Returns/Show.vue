<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    returnRecord: Object,
});
</script>

<template>
    <Head title="Return Details - SaaSMedi" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-20">
        <TopNavbar />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-6">
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Reference No.</div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ returnRecord.reference_no }}</h2>
                        <div class="mt-4 space-y-1">
                            <div class="text-sm"><span class="font-medium text-slate-500">Return Date:</span> {{ returnRecord.return_date }}</div>
                            <div class="text-sm flex items-center gap-2">
                                <span class="font-medium text-slate-500">Status:</span> 
                                <span v-if="returnRecord.status === 'approved'" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Approved</span>
                                <span v-else class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20 capitalize">{{ returnRecord.status }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-left md:text-right">
                        <div class="text-sm text-slate-500 mb-1">Supplier Details</div>
                        <div class="font-bold text-lg text-slate-900">{{ returnRecord.supplier.name }}</div>
                        <div class="text-sm text-slate-600">{{ returnRecord.supplier.companies ? returnRecord.supplier.companies.join(', ') : 'Individual' }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ returnRecord.supplier.phone }}</div>
                        <div class="text-sm text-slate-500">{{ returnRecord.supplier.email }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-8">Item</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Batch & Expiry</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Return Qty</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reason</th>
                                <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pr-8">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr v-for="item in returnRecord.items" :key="item.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-8">
                                    <div class="font-bold text-slate-900">{{ item.medicine.name }}</div>
                                    <div class="text-xs text-slate-500">{{ item.medicine.generic_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-sm text-slate-700">Batch: <span class="font-semibold">{{ item.batch_no }}</span></div>
                                    <div class="text-xs text-rose-500">Exp: {{ item.expiry_date }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2.5rem] rounded-md bg-slate-100 px-2.5 py-1 text-sm font-bold text-slate-900">
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right">
                                    <div class="text-sm text-slate-700">${{ parseFloat(item.unit_price).toFixed(2) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-left">
                                    <div class="text-sm font-medium text-slate-700"><span class="font-bold">{{ item.return_reason?.code }}</span> - {{ item.return_reason?.reason }}</div>
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right sm:pr-8">
                                    <div class="text-sm font-bold text-rose-600">${{ parseFloat(item.subtotal).toFixed(2) }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-50 border-t border-slate-200 p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between gap-8">
                        <div class="w-full sm:w-1/2">
                            <h3 class="text-sm font-semibold text-slate-900 mb-2">Return Notes</h3>
                            <div class="bg-white p-4 rounded-xl border border-slate-200 text-sm text-slate-600 min-h-[100px]">
                                {{ returnRecord.notes || 'No additional notes provided.' }}
                            </div>
                        </div>
                        <div class="w-full sm:w-1/2 max-w-sm ml-auto space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Total Return Items:</span>
                                <span class="font-medium text-slate-900">{{ returnRecord.items.length }}</span>
                            </div>
                            <hr class="border-slate-200 my-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-slate-900">Total Return Value:</span>
                                <span class="text-xl font-black text-rose-600">${{ parseFloat(returnRecord.total_amount).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Credit Notes Section -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                        Generated Credit Notes
                    </h2>
                </div>
                <div class="p-6">
                    <div v-if="returnRecord.credit_notes && returnRecord.credit_notes.length > 0">
                        <div v-for="note in returnRecord.credit_notes" :key="note.id" class="flex justify-between items-center border border-indigo-100 bg-indigo-50/30 rounded-xl p-4">
                            <div>
                                <div class="font-bold text-indigo-900">{{ note.credit_note_no }}</div>
                                <div class="text-xs text-indigo-600 mt-1">Status: <span class="uppercase font-semibold">{{ note.status }}</span></div>
                            </div>
                            <div class="text-lg font-black text-indigo-700">${{ parseFloat(note.amount).toFixed(2) }}</div>
                        </div>
                    </div>
                    <div v-else class="text-center text-slate-500 py-4 text-sm">
                        No credit notes associated with this return.
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
