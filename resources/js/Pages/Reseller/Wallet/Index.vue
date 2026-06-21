<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    transactions: Object,
    balance: [Number, String]
});
</script>

<template>
    <Head title="Wallet Ledger - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-2xl font-black text-slate-900">Wallet Ledger</h2>
            
            <div class="bg-white px-6 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="text-sm font-bold text-slate-500">Available Balance:</div>
                <div class="text-2xl font-black text-emerald-600">৳{{ Number(balance).toLocaleString() }}</div>
                <Link :href="route('reseller.withdrawals.index')" class="ml-4 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-sm transition-colors">
                    Withdraw
                </Link>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold w-48">Date</th>
                            <th class="p-4 font-bold">Description</th>
                            <th class="p-4 font-bold text-right">Amount</th>
                            <th class="p-4 font-bold text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="tx in transactions.data" :key="tx.id" class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm text-slate-600">
                                {{ new Date(tx.created_at).toLocaleString() }}
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ tx.description }}</div>
                                <div class="text-xs text-slate-500 uppercase tracking-wider mt-1">{{ tx.reference_type }} <span v-if="tx.reference_id">#{{ tx.reference_id }}</span></div>
                            </td>
                            <td class="p-4 text-right font-black" :class="tx.type === 'credit' ? 'text-emerald-600' : 'text-red-500'">
                                {{ tx.type === 'credit' ? '+' : '-' }}৳{{ Number(tx.amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-right font-bold text-slate-900">
                                ৳{{ Number(tx.balance_after).toLocaleString() }}
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="4" class="p-8 text-center text-slate-500 font-medium">No transactions recorded yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50" v-if="transactions.links">
                <div class="flex items-center gap-1">
                    <Link v-for="(link, i) in transactions.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1 rounded border text-sm font-medium transition-colors"
                        :class="[link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
