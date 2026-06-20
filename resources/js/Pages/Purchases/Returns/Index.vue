<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    returns: Object,
});
</script>

<template>
    <Head title="Purchase Returns - MediSaaS" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <div class="flex items-center gap-3">
                        <Link :href="route('purchases.index')" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </Link>
                        <h1 class="text-3xl font-bold text-slate-900">Purchase Returns</h1>
                    </div>
                    <p class="mt-2 text-sm text-slate-500 ml-9">Manage returned medicines and supplier credit notes.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('purchase-returns.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                        </svg>
                        New Return
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Ref & Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Supplier</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Return Value</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="ret in returns.data" :key="ret.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="font-semibold text-slate-900">{{ ret.reference_no }}</div>
                                    <div class="text-sm text-slate-500">{{ ret.return_date }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-sm text-slate-900 font-medium">{{ ret.supplier?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ ret.supplier?.companies ? ret.supplier.companies.join(', ') : 'Individual' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right">
                                    <div class="text-sm font-bold text-rose-600">${{ parseFloat(ret.total_amount).toFixed(2) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <span v-if="ret.status === 'approved'" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Approved</span>
                                    <span v-else-if="ret.status === 'pending'" class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20 capitalize">{{ ret.status }}</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 flex justify-end items-center">
                                    <Link :href="route('purchase-returns.show', ret.id)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                        View Details
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="returns.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <p class="text-lg font-medium text-slate-900">No returns found</p>
                                    <p class="mt-1">When you return medicines to suppliers, they will appear here.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
