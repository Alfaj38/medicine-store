<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    companies: Object
});
</script>

<template>
    <Head title="Referred Companies - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Referred Companies</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Track the onboarding and subscription status of companies you've referred.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-[15px] text-slate-800">Company Roster</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-white border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-widest">
                            <th class="py-4 px-5 font-bold">Company</th>
                            <th class="py-4 px-5 font-bold">Registration Date</th>
                            <th class="py-4 px-5 font-bold">Code Used</th>
                            <th class="py-4 px-5 font-bold">Current Plan</th>
                            <th class="py-4 px-5 font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="ref in companies.data" :key="ref.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[#00b67a]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-800">{{ ref.company.name }}</div>
                                        <div class="text-[11px] font-medium text-slate-500 mt-0.5">{{ ref.company.phone || ref.company.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-xs font-bold text-slate-800">{{ new Date(ref.registered_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</div>
                                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mt-0.5">{{ new Date(ref.registered_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-mono text-xs font-bold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg inline-block border border-slate-200">
                                    {{ ref.referral_code?.code || 'Direct Link' }}
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-xs font-bold text-slate-800">{{ ref.company.subscription?.plan?.name || 'Trial / Free' }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ ref.company.subscription?.billing_cycle || 'N/A' }}</div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                    :class="ref.company.subscription?.status === 'active' || ref.company.registration_status === 'active' ? 'bg-[#00b67a]/10 text-[#00b67a]' : 'bg-slate-100 text-slate-500'">
                                    {{ ref.company.subscription?.status || ref.company.registration_status || 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!companies.data.length">
                            <td colspan="5" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 mx-auto flex items-center justify-center text-slate-300 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="text-sm font-bold text-slate-600">No referred companies yet</div>
                                <div class="text-[11px] font-medium text-slate-400 mt-1">Start sharing your referral codes to grow your network.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50" v-if="companies.links && companies.links.length > 3">
                <div class="flex items-center gap-1.5">
                    <Link v-for="(link, i) in companies.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1.5 rounded-lg border text-sm font-bold transition-all shadow-sm"
                        :class="[link.active ? 'bg-[#00b67a] text-white border-[#00b67a]' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300', !link.url ? 'opacity-50 cursor-not-allowed shadow-none' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
