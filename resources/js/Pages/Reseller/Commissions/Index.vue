<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    commissions: Object
});
</script>

<template>
    <Head title="Commissions History - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">Commissions History</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold">Company</th>
                            <th class="p-4 font-bold">Event Type</th>
                            <th class="p-4 font-bold text-right">Payment Amt</th>
                            <th class="p-4 font-bold text-right">Rate</th>
                            <th class="p-4 font-bold text-right">Commission Earned</th>
                            <th class="p-4 font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="comm in commissions.data" :key="comm.id" class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm text-slate-600">
                                {{ new Date(comm.created_at).toLocaleString() }}
                            </td>
                            <td class="p-4 font-bold text-slate-900">
                                {{ comm.company?.name || 'Unknown Company' }}
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm font-medium text-slate-700">{{ comm.event_type }}</span>
                            </td>
                            <td class="p-4 text-right font-medium text-slate-700">
                                ৳{{ Number(comm.payment_amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-right text-sm text-slate-500">
                                {{ Number(comm.commission_rate) }}%
                            </td>
                            <td class="p-4 text-right font-black text-emerald-600">
                                <span v-if="comm.status === 'reversed'" class="text-red-500 line-through">
                                    ৳{{ Number(comm.commission_amount).toLocaleString() }}
                                </span>
                                <span v-else>
                                    +৳{{ Number(comm.commission_amount).toLocaleString() }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                    :class="{
                                        'bg-amber-100 text-amber-800': comm.status === 'pending',
                                        'bg-emerald-100 text-emerald-800': comm.status === 'paid',
                                        'bg-red-100 text-red-800': comm.status === 'reversed'
                                    }">
                                    {{ comm.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!commissions.data.length">
                            <td colspan="7" class="p-8 text-center text-slate-500 font-medium">No commissions earned yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50" v-if="commissions.links">
                <div class="flex items-center gap-1">
                    <Link v-for="(link, i) in commissions.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1 rounded border text-sm font-medium transition-colors"
                        :class="[link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
