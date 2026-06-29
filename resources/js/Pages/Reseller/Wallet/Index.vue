<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    transactions: Object,
    balance: [Number, String],
    pendingWithdrawal: [Number, String],
    totalPaid: [Number, String]
});
</script>

<template>
    <Head title="Wallet & Withdrawals - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Wallet & Withdrawals</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Manage your earnings, view ledger history, and request payouts.</p>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <!-- Available Balance -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Available Balance</span>
                </div>
                <div class="text-3xl font-black text-slate-800">৳{{ Number(balance || 0).toLocaleString() }}</div>
                
                <div class="absolute right-6 top-1/2 -translate-y-1/2">
                    <Link :href="route('reseller.withdrawals.index')" class="px-5 py-2.5 bg-[#00b67a] hover:bg-[#00a06b] text-white rounded-xl font-bold text-sm transition-colors shadow-sm shadow-[#00b67a]/20">
                        Withdraw
                    </Link>
                </div>
            </div>

            <!-- Pending Withdrawal -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pending Request</span>
                </div>
                <div class="text-3xl font-black text-slate-800">৳{{ Number(pendingWithdrawal || 0).toLocaleString() }}</div>
            </div>

            <!-- Total Paid -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Paid Out</span>
                </div>
                <div class="text-3xl font-black text-slate-800">৳{{ Number(totalPaid || 0).toLocaleString() }}</div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-[15px] text-slate-800">Wallet Ledger</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-widest">
                            <th class="py-4 px-5 font-bold">Date</th>
                            <th class="py-4 px-5 font-bold">Description</th>
                            <th class="py-4 px-5 font-bold text-right">Amount</th>
                            <th class="py-4 px-5 font-bold text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="tx in transactions.data" :key="tx.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5">
                                <div class="text-xs font-bold text-slate-800">{{ new Date(tx.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</div>
                                <div class="text-[10px] font-medium text-slate-400">{{ new Date(tx.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <span v-if="tx.type === 'credit'" class="w-1.5 h-1.5 rounded-full bg-[#00b67a]"></span>
                                    <span v-else class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ tx.description }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ tx.reference_type }} <span v-if="tx.reference_id">#{{ tx.reference_id }}</span></div>
                            </td>
                            <td class="py-4 px-5 text-right font-black" :class="tx.type === 'credit' ? 'text-[#00b67a]' : 'text-red-500'">
                                <div class="flex items-center justify-end gap-1">
                                    <span>{{ tx.type === 'credit' ? '+' : '-' }}</span>
                                    <span>৳{{ Number(tx.amount).toLocaleString() }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-right font-bold text-slate-800">
                                ৳{{ Number(tx.balance_after).toLocaleString() }}
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="4" class="py-8 text-center text-sm text-slate-500 font-medium">No transactions recorded yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50" v-if="transactions.links && transactions.links.length > 3">
                <div class="flex items-center gap-1.5">
                    <Link v-for="(link, i) in transactions.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1.5 rounded-lg border text-sm font-bold transition-all shadow-sm"
                        :class="[link.active ? 'bg-[#00b67a] text-white border-[#00b67a]' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300', !link.url ? 'opacity-50 cursor-not-allowed shadow-none' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
