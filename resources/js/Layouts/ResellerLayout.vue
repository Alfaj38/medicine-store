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
    { name: 'Payout History', href: route('reseller.withdrawals.index'), icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Reports', href: route('reseller.reports'), icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Marketing Resources', href: route('reseller.marketing'), icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z' },
    { name: 'Settings', href: route('reseller.settings'), icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
];

const isActive = (href) => {
    if (href === '#') return false;
    return route().current(href.split('.').slice(0, 2).join('.') + '*');
};

import { ref } from 'vue';
const showProfileMenu = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] flex font-sans text-slate-900 selection:bg-[#00b67a] selection:text-white">
        <!-- Sidebar -->
        <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-slate-100 flex-shrink-0 h-screen sticky top-0 overflow-y-auto z-20">
            <!-- Logo -->
            <div class="px-6 py-6 border-b border-slate-50">
                <Link :href="route('home')" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#00b67a] flex items-center justify-center">
                        <span class="text-white font-black text-xl leading-none">+</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-xl tracking-tight text-[#024329]">SaaS<span class="text-[#00b67a]">Medi</span></span>
                        <span class="text-[10px] font-bold text-[#00b67a] tracking-widest uppercase">Partner Portal</span>
                    </div>
                </Link>
            </div>

            <div class="flex-1 px-4 py-4 space-y-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14px] font-semibold transition-colors"
                    :class="isActive(item.href) ? 'text-[#00b67a] bg-emerald-50/50' : 'text-slate-500 hover:text-[#00b67a] hover:bg-slate-50'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </div>

            <!-- Bottom Promo Box -->
            <div class="px-4 pb-4">
                <div class="bg-gradient-to-br from-emerald-50 to-green-50/30 rounded-2xl p-4 text-center border border-emerald-100/50">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Earn More Rewards!</h4>
                    <p class="text-xs text-slate-500 mb-3 leading-relaxed">Share your codes and earn up to <span class="font-bold text-[#00b67a]">10% commission</span> on every referral.</p>
                    <div class="w-12 h-12 mx-auto mb-3 text-[#00b67a]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><path d="M12 7v14"/></svg>
                    </div>
                    <button class="w-full py-2 bg-[#00b67a] text-white text-xs font-bold rounded-lg hover:bg-[#009b68] transition-colors">
                        View Marketing Tools
                    </button>
                </div>
            </div>

            <!-- Bottom Cards -->
            <div class="px-4 py-4 space-y-3 border-t border-slate-100 mt-auto">
                <div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-500 mb-0.5">Commission Rate</div>
                        <div class="text-xl font-black text-[#00b67a]">{{ reseller?.commission_rate || 10 }}%</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-400 font-medium">Active Since</div>
                        <div class="text-[11px] font-bold text-slate-700">May 20, 2024</div>
                    </div>
                </div>
                
                <Link :href="route('reseller.logout')" method="post" as="button" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 rounded-xl text-[13px] font-bold text-red-600 transition-colors">
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
                <div class="w-6 h-6 rounded-md bg-[#00b67a] flex items-center justify-center">
                    <span class="text-white font-black text-sm leading-none">+</span>
                </div>
                <span class="font-extrabold text-[20px] tracking-tight text-[#00b67a]">SaaS<span class="text-slate-800">Medi</span></span>
            </Link>
            <div class="w-8"></div> <!-- Spacer for centering -->
        </header>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-0 flex flex-col min-h-screen pt-16 lg:pt-0 w-full overflow-hidden">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 min-h-[5rem] hidden lg:flex items-center justify-between px-8 sticky top-0 z-10 py-4">
                <div>
                    <h1 class="text-[22px] font-bold text-slate-800 tracking-tight leading-tight">Partner Dashboard</h1>
                    <div class="text-sm font-medium text-slate-500 mt-0.5">Welcome back, <span class="font-bold text-slate-700">{{ reseller?.name || 'Pharma Point' }}</span>! 👋</div>
                </div>
                
                <div class="flex items-center gap-5">
                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                    
                    <!-- Profile Menu -->
                    <div class="relative">
                        <!-- Backdrop -->
                        <div v-if="showProfileMenu" @click="showProfileMenu = false" class="fixed inset-0 z-40"></div>
                        
                        <div @click="showProfileMenu = !showProfileMenu" class="flex items-center gap-3 cursor-pointer group relative z-50">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                                {{ reseller?.name?.charAt(0) || 'P' }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-[13px] font-bold text-slate-900 leading-tight group-hover:text-[#00b67a] transition-colors">{{ reseller?.name || 'Pharma Point' }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">Partner</div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 hidden md:block transition-transform" :class="{'rotate-180': showProfileMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                        
                        <!-- Dropdown panel -->
                        <div v-show="showProfileMenu" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-medium text-slate-500">Signed in as</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ reseller?.email || 'partner@example.com' }}</p>
                            </div>
                            <Link :href="route('reseller.settings')" @click="showProfileMenu = false" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#00b67a] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                Account Settings
                            </Link>
                            <Link :href="route('reseller.wallet')" @click="showProfileMenu = false" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#00b67a] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Wallet Balance
                            </Link>
                            <div class="h-px bg-slate-100 my-1"></div>
                            <Link :href="route('reseller.logout')" method="post" as="button" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Sign out
                            </Link>
                        </div>
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
