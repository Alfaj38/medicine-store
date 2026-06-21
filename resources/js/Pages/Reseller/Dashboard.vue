<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    stats: Object,
    chartData: Array,
    reseller: Object,
});
</script>

<template>
    <Head title="Dashboard - Partner Portal" />
    <ResellerLayout>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Balance Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <Link :href="route('reseller.withdrawals.index')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Withdraw</Link>
                </div>
                <div class="text-sm font-bold text-slate-500 mb-1">Available Balance</div>
                <div class="text-3xl font-black text-slate-900">৳{{ Number(stats.wallet_balance).toLocaleString() }}</div>
                <div class="text-xs font-bold text-amber-500 mt-2" v-if="stats.pending_withdrawal > 0">
                    ৳{{ Number(stats.pending_withdrawal).toLocaleString() }} pending withdrawal
                </div>
            </div>

            <!-- This Month Earnings -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-sm font-bold text-slate-500 mb-1">This Month</div>
                <div class="text-3xl font-black text-slate-900">৳{{ Number(stats.monthly_earnings).toLocaleString() }}</div>
            </div>

            <!-- Lifetime Earnings -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="text-sm font-bold text-slate-500 mb-1">Lifetime Earnings</div>
                <div class="text-3xl font-black text-slate-900">৳{{ Number(stats.lifetime_earnings).toLocaleString() }}</div>
            </div>

            <!-- Total Referrals -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="text-sm font-bold text-slate-500 mb-1">Referred Companies</div>
                <div class="text-3xl font-black text-slate-900">{{ stats.total_companies }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Getting Started / Quick Links -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-lg text-slate-900">Your Primary Link</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-4">Share this link with potential pharmacies. When they sign up, they are permanently linked to your account.</p>
                    
                    <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200 mb-4">
                        <span class="text-sm font-mono text-slate-700 flex-1 truncate">{{ route('referral.link', reseller.reseller_code) }}</span>
                    </div>

                    <Link :href="route('reseller.codes.index')" class="w-full block text-center py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-xl font-bold transition-colors text-sm">
                        Manage Promo Codes
                    </Link>
                </div>
            </div>

            <!-- Earnings Chart (Placeholder representation) -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900">Earnings Overview (Last 6 Months)</h3>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between gap-2">
                        <div v-for="item in chartData" :key="item.month" class="flex-1 flex flex-col items-center gap-2">
                            <!-- Bar -->
                            <div class="w-full bg-emerald-100 rounded-t-md relative group">
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-slate-800 text-white text-xs font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                                    ৳{{ Number(item.earnings).toLocaleString() }}
                                </div>
                                <div class="w-full bg-emerald-500 rounded-t-md transition-all duration-500" 
                                    :style="`height: ${Math.max((item.earnings / (Math.max(...chartData.map(d => d.earnings)) || 1)) * 100, 5)}%`"></div>
                            </div>
                            <div class="text-xs font-bold text-slate-400">{{ item.month }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
