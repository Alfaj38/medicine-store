<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const reseller = computed(() => page.props.auth?.user || {});

const navigation = [
    { name: 'Dashboard', href: route('reseller.dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Referral Codes', href: route('reseller.codes.index'), icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' },
    { name: 'Referred Companies', href: route('reseller.companies'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Commissions', href: route('reseller.commissions'), icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Wallet & Withdrawals', href: route('reseller.wallet'), icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
];

const isActive = (href) => {
    return route().current(href.split('.').slice(0, 2).join('.') + '*');
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-sans">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-900 text-white flex-shrink-0 fixed inset-y-0 left-0 flex flex-col z-20">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center font-black text-xl shadow-lg">+</div>
                    <div>
                        <div class="font-bold text-lg leading-tight">Partner</div>
                        <div class="text-xs text-emerald-400 font-medium">MediSaaS</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <Link v-for="item in navigation" :key="item.name" :href="item.href"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium text-sm"
                        :class="isActive(item.href) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </div>
            </div>

            <div class="mt-auto p-6">
                <div class="bg-slate-800 rounded-2xl p-4 border border-slate-700">
                    <div class="text-xs text-slate-400 mb-1">Commission Rate</div>
                    <div class="text-2xl font-black text-emerald-400">{{ reseller?.commission_rate || 10 }}%</div>
                </div>
                
                <Link :href="route('reseller.logout')" method="post" as="button" class="w-full mt-4 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-red-400 hover:text-white hover:bg-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-10">
                <h1 class="text-xl font-black text-slate-900">Partner Dashboard</h1>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm font-bold text-slate-900">{{ reseller?.name }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ reseller?.reseller_code }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        {{ reseller?.name?.charAt(0) || 'P' }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
