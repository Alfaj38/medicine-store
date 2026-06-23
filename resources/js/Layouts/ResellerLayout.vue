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
    <div class="min-h-screen bg-slate-50 flex font-sans text-slate-900 selection:bg-emerald-500 selection:text-white">
        <!-- Sidebar -->
        <aside class="hidden lg:flex flex-col w-72 bg-white border-r border-slate-100 flex-shrink-0 h-screen sticky top-0 overflow-y-auto z-20">
            <!-- Logo -->
            <div class="px-6 py-8">
                <Link :href="route('home')" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <span class="text-white font-black text-xl leading-none">+</span>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-emerald-600">SaaS<span class="text-slate-800">Medi</span></span>
                </Link>
                <div class="mt-2 text-xs font-bold text-emerald-500 tracking-wider uppercase ml-11">Partner Portal</div>
            </div>

            <div class="flex-1 px-4 py-2 space-y-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.href"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold transition-colors"
                    :class="isActive(item.href) ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-emerald-600 hover:bg-slate-50'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </div>

            <!-- Bottom Cards -->
            <div class="px-6 py-6 space-y-4 border-t border-slate-100 mt-auto">
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-[13px] font-semibold text-slate-600 mb-1">Commission Rate</div>
                    <div class="text-2xl font-black text-emerald-600">{{ reseller?.commission_rate || 10 }}%</div>
                </div>
                
                <Link :href="route('reseller.logout')" method="post" as="button" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 rounded-xl text-[13px] font-bold text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Mobile Top Header -->
        <header class="lg:hidden fixed top-0 w-full bg-white z-50 border-b border-slate-100 h-16 flex items-center justify-between px-4">
            <button class="text-slate-700 p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <Link :href="route('home')" class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-emerald-600 flex items-center justify-center">
                    <span class="text-white font-black text-sm leading-none">+</span>
                </div>
                <span class="font-extrabold text-[20px] tracking-tight text-emerald-600">SaaS<span class="text-slate-800">Medi</span></span>
            </Link>
            <div class="w-8"></div> <!-- Spacer for centering -->
        </header>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-0 flex flex-col min-h-screen pt-16 lg:pt-0 w-full overflow-hidden">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 h-20 hidden lg:flex items-center justify-between px-8 sticky top-0 z-10">
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Partner Dashboard</h1>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-[15px] font-bold text-slate-900 leading-tight">{{ reseller?.name }}</div>
                        <div class="text-xs text-slate-500 font-semibold">{{ reseller?.reseller_code }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shadow-inner">
                        {{ reseller?.name?.charAt(0) || 'P' }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-6 lg:p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
