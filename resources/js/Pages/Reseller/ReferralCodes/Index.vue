<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    codes: Array
});

const form = useForm({
    label: ''
});

const generate = () => {
    form.post(route('reseller.codes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const toggle = (id) => {
    useForm({}).patch(route('reseller.codes.toggle', id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Referral Codes - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Referral Codes</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Generate and manage tracking links for your promotional campaigns.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-[15px] text-slate-800">Generate New Code</h3>
                    <p class="text-[12px] font-medium text-slate-500 mt-0.5">Create codes for different marketing campaigns to track performance.</p>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="generate" class="flex flex-col md:flex-row gap-4">
                    <input v-model="form.label" type="text" placeholder="Campaign Label (e.g. Facebook Ads Q3)" class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white font-medium text-slate-800">
                    <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition-all disabled:opacity-70 whitespace-nowrap shadow-sm">
                        Generate Code
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-[15px] text-slate-800">Active Campaigns</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-widest">
                            <th class="py-4 px-5 font-bold">Code / Label</th>
                            <th class="py-4 px-5 font-bold text-center">Conversions</th>
                            <th class="py-4 px-5 font-bold text-right">Commission</th>
                            <th class="py-4 px-5 font-bold text-center">Status</th>
                            <th class="py-4 px-5 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="code in codes" :key="code.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-800">{{ code.label || 'Default Campaign' }}</div>
                                        <div class="text-[11px] font-mono font-medium text-slate-500 mt-0.5">{{ code.code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="font-black text-slate-800 text-sm">{{ code.total_companies || 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Referrals</div>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="font-black text-[#00b67a] text-sm">৳{{ Number(code.total_commission || 0).toLocaleString() }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Earned</div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                    :class="code.status === 'active' ? 'bg-[#00b67a]/10 text-[#00b67a]' : 'bg-slate-100 text-slate-500'">
                                    {{ code.status }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <button @click="toggle(code.id)" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border transition-all"
                                    :class="code.status === 'active' ? 'border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300' : 'border-[#00b67a]/20 text-[#00b67a] hover:bg-[#00b67a]/10'">
                                    {{ code.status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!codes.length">
                            <td colspan="5" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 mx-auto flex items-center justify-center text-slate-300 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <div class="text-sm font-bold text-slate-600">No campaigns yet</div>
                                <div class="text-[11px] font-medium text-slate-400 mt-1">Generate your first code above to start tracking.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </ResellerLayout>
</template>
