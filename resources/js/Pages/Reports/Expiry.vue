<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    inventory: Object,
    stats: Object,
    now: String,
});

const getExpiryStatusClass = (dateString) => {
    if (!dateString) return 'bg-slate-100 text-slate-800';
    
    const expiry = new Date(dateString);
    const today = new Date(props.now);
    
    // Time difference in months
    const diffTime = expiry - today;
    const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30);
    
    if (diffMonths < 0) {
        return 'bg-rose-100 text-rose-800 font-bold'; // Expired
    } else if (diffMonths <= 3) {
        return 'bg-orange-100 text-orange-800 font-semibold'; // Expires within 3 months
    } else if (diffMonths <= 6) {
        return 'bg-amber-100 text-amber-800'; // Expires within 6 months
    } else {
        return 'bg-emerald-100 text-emerald-800'; // Safe
    }
};

const getExpiryLabel = (dateString) => {
    if (!dateString) return 'No Date';
    
    const expiry = new Date(dateString);
    const today = new Date(props.now);
    
    const diffTime = expiry - today;
    const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30);
    
    if (diffMonths < 0) return 'Expired';
    if (diffMonths <= 3) return '< 3 Months';
    if (diffMonths <= 6) return '< 6 Months';
    return 'Safe';
};
</script>

<template>
    <Head title="Expiry Dashboard - SaaSMedi" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Batch & Expiry Management</h1>
                <p class="mt-2 text-sm text-slate-500">Monitor medicine batches to prevent selling expired products and reduce wastage.</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Already Expired</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-rose-600">{{ stats.already_expired }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-rose-50 px-5 py-3 border-t border-rose-100 text-sm">
                        <a href="#" class="font-medium text-rose-700 hover:text-rose-900">Review immediately &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Expiring in &lt; 3 Months</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-orange-600">{{ stats.expiring_in_3_months }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-orange-50 px-5 py-3 border-t border-orange-100 text-sm">
                        <a href="#" class="font-medium text-orange-700 hover:text-orange-900">Plan promotional sales &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500 truncate">Expiring in 3-6 Months</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-bold text-amber-600">{{ stats.expiring_in_6_months - stats.expiring_in_3_months }}</div>
                                        <div class="ml-2 text-sm text-slate-500">Batches</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-amber-50 px-5 py-3 border-t border-amber-100 text-sm">
                        <span class="font-medium text-amber-700">Monitor closely</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-5 border-b border-slate-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Inventory Expiry Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Medicine</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Batch No.</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Quantity Left</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Expiry Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="item in inventory.data" :key="item.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="font-semibold text-slate-900">{{ item.medicine?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ item.medicine?.generic_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                                    {{ item.batch_no || 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-900">
                                    {{ item.quantity }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                                    {{ item.expiry_date }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium" :class="getExpiryStatusClass(item.expiry_date)">
                                        {{ getExpiryLabel(item.expiry_date) }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="inventory.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <p class="text-lg font-medium text-slate-900">No batch data found</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
