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
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Commissions Ledger</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Detailed history of all generated commissions from your referred companies.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-[15px] text-slate-800">Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-widest">
                            <th class="py-4 px-5 font-bold">Date</th>
                            <th class="py-4 px-5 font-bold">Company</th>
                            <th class="py-4 px-5 font-bold">Event Type</th>
                            <th class="py-4 px-5 font-bold text-right">Payment</th>
                            <th class="py-4 px-5 font-bold text-right">Rate</th>
                            <th class="py-4 px-5 font-bold text-right">Commission Earned</th>
                            <th class="py-4 px-5 font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="comm in commissions.data" :key="comm.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5">
                                <div class="text-xs font-bold text-slate-800">{{ new Date(comm.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</div>
                                <div class="text-[10px] font-medium text-slate-400 mt-0.5">{{ new Date(comm.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-sm text-slate-800">{{ comm.company?.name || 'Unknown Company' }}</div>
                                <div class="text-[10px] font-mono font-medium text-slate-500 mt-0.5" v-if="comm.referral?.referral_code">Via Code: {{ comm.referral.referral_code.code }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="capitalize text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ comm.event_type }}</span>
                            </td>
                            <td class="py-4 px-5 text-right font-medium text-slate-700">
                                ৳{{ Number(comm.payment_amount).toLocaleString() }}
                            </td>
                            <td class="py-4 px-5 text-right text-xs font-bold text-slate-500">
                                {{ Number(comm.commission_rate) }}%
                            </td>
                            <td class="py-4 px-5 text-right font-black">
                                <span v-if="comm.status === 'reversed'" class="text-red-500 line-through">
                                    ৳{{ Number(comm.commission_amount).toLocaleString() }}
                                </span>
                                <span v-else class="text-[#00b67a]">
                                    +৳{{ Number(comm.commission_amount).toLocaleString() }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                    :class="{
                                        'bg-amber-50 text-amber-600': comm.status === 'pending',
                                        'bg-[#00b67a]/10 text-[#00b67a]': comm.status === 'paid',
                                        'bg-red-50 text-red-500': comm.status === 'reversed'
                                    }">
                                    {{ comm.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!commissions.data.length">
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 mx-auto flex items-center justify-center text-slate-300 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-sm font-bold text-slate-600">No commissions yet</div>
                                <div class="text-[11px] font-medium text-slate-400 mt-1">When referred companies make payments, your commissions will appear here.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50" v-if="commissions.links && commissions.links.length > 3">
                <div class="flex items-center gap-1.5">
                    <Link v-for="(link, i) in commissions.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1.5 rounded-lg border text-sm font-bold transition-all shadow-sm"
                        :class="[link.active ? 'bg-[#00b67a] text-white border-[#00b67a]' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300', !link.url ? 'opacity-50 cursor-not-allowed shadow-none' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
