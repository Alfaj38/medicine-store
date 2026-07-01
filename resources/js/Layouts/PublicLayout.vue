<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-sans text-slate-900 selection:bg-emerald-500 selection:text-white">
        
        <!-- ================= Desktop/Tablet Left Sidebar ================= -->
        <aside class="hidden lg:flex flex-col w-72 bg-white border-r border-slate-100 flex-shrink-0 h-screen sticky top-0 overflow-y-auto z-20">
            <!-- Logo -->
            <div class="px-6 py-8">
                <Link :href="route('home')" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <span class="text-white font-black text-xl leading-none">+</span>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-emerald-600">SaaS<span class="text-slate-800">Medi</span></span>
                </Link>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 px-4 py-2 space-y-2">
                <a href="/#pharmacies" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold text-emerald-600 bg-emerald-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Online Shop
                </a>
                <Link :href="route('features')" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold text-slate-600 hover:text-emerald-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Features
                </Link>
                <Link :href="route('pricing')" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold text-slate-600 hover:text-emerald-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pricing
                </Link>
                <Link :href="route('partner')" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold text-slate-600 hover:text-emerald-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Partner Program
                </Link>
                <Link :href="route('track.order.page')" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-[15px] font-bold text-slate-600 hover:text-emerald-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Track Order
                </Link>
            </div>

            <!-- Bottom Cards in Sidebar -->
            <div class="px-6 py-6 space-y-4 border-t border-slate-100">
                <template v-if="$page.props.auth?.user">
                    <!-- User Profile & Package Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 text-center shadow-sm">
                        <p class="text-[13px] font-semibold text-slate-600 mb-1">Signed in as</p>
                        <p class="text-[15px] font-bold text-slate-900 mb-3 truncate">{{ $page.props.auth.user.name }}</p>
                        
                        <div v-if="$page.props.auth.user.company?.subscription" class="mb-4 p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs text-slate-500 mb-0.5">Current Plan</p>
                            <p class="text-sm font-bold text-emerald-600">
                                {{ $page.props.auth.user.company.subscription.package?.name || 'Trial' }}
                            </p>
                            <p v-if="$page.props.auth.user.company.subscription.status === 'trial'" class="text-[10px] text-orange-500 font-bold mt-1 uppercase tracking-wider">Trial Version</p>
                        </div>
                        
                        <Link :href="route('dashboard')" class="block w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-bold rounded-xl transition-colors shadow-sm mb-2">
                            Go to Dashboard
                        </Link>
                        
                        <Link v-if="$page.props.auth.user.company?.subscription?.status === 'trial' || $page.props.auth.user.company?.subscription?.plan?.code === 'free_trial'" :href="route('pricing')" class="block w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-[13px] font-bold rounded-xl transition-colors shadow-sm">
                            Upgrade Package
                        </Link>
                    </div>
                </template>
                <template v-else>
                    <!-- Sign In Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 text-center shadow-sm">
                        <p class="text-[13px] font-semibold text-slate-600 mb-3">Sign in to your account</p>
                        <Link :href="route('login')" class="block w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                            Sign In
                        </Link>
                    </div>
                    
                    <!-- Free Trial Card -->
                    <div class="bg-[#f0fdf4] border border-emerald-100 rounded-2xl p-5 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-200 rounded-full opacity-50 blur-xl"></div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Start your 15-day</p>
                        <h4 class="text-[20px] font-extrabold text-slate-900 mb-3">Free Trial</h4>
                        <ul class="text-[11px] text-slate-600 space-y-2 mb-5 font-medium">
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                No credit card required
                            </li>
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Setup in 5 minutes
                            </li>
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Trusted by 500+ pharmacies
                            </li>
                        </ul>
                        <Link :href="route('register')" class="block w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-bold rounded-xl text-center transition-colors shadow-sm relative z-10">
                            Start Free Trial
                        </Link>
                    </div>
                </template>

                <div class="text-[11px] text-slate-400 font-medium pt-2">
                    &copy; {{ new Date().getFullYear() }} SaaSMedi.<br>All rights reserved.
                </div>
            </div>
        </aside>

        <!-- ================= Mobile Top Header ================= -->
        <header class="lg:hidden fixed top-0 w-full bg-white z-50 border-b border-slate-100 h-16 flex items-center justify-between px-4">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-700 p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <Link :href="route('home')" class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-emerald-600 flex items-center justify-center">
                    <span class="text-white font-black text-sm leading-none">+</span>
                </div>
                <span class="font-extrabold text-[20px] tracking-tight text-emerald-600">SaaS<span class="text-slate-800">Medi</span></span>
            </Link>
            <button class="relative text-slate-700 p-2 -mr-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="absolute top-1.5 right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-[1.5px] border-white text-[8px] font-bold text-white flex items-center justify-center">0</span>
            </button>
        </header>

        <!-- ================= Mobile Drawer (Sidebar) ================= -->
        <div v-show="mobileMenuOpen" class="lg:hidden fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
        <div class="lg:hidden fixed inset-y-0 left-0 w-[280px] bg-white shadow-2xl z-[101] transform transition-transform duration-300 flex flex-col" :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="px-5 py-5 border-b border-slate-100 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-emerald-600 flex items-center justify-center">
                        <span class="text-white font-black text-sm leading-none">+</span>
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-emerald-600">SaaS<span class="text-slate-800">Medi</span></span>
                </Link>
                <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                <a href="/#pharmacies" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[15px] font-bold text-emerald-600 bg-emerald-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Online Shop
                </a>
                <Link :href="route('features')" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[15px] font-bold text-slate-700 hover:bg-slate-50">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Features
                </Link>
                <Link :href="route('pricing')" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[15px] font-bold text-slate-700 hover:bg-slate-50">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pricing
                </Link>
                <Link :href="route('partner')" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[15px] font-bold text-slate-700 hover:bg-slate-50">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Partner Program
                </Link>
                <Link :href="route('track.order.page')" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[15px] font-bold text-slate-700 hover:bg-slate-50">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Track Order
                </Link>
            </div>
            
            <div class="p-5 border-t border-slate-100 space-y-3 bg-slate-50/50">
                <template v-if="$page.props.auth?.user">
                    <div class="bg-white border border-slate-200 rounded-xl p-3 text-center mb-2 shadow-sm">
                        <p class="text-xs text-slate-500">Signed in as <span class="font-bold text-slate-800">{{ $page.props.auth.user.name }}</span></p>
                        <div v-if="$page.props.auth.user.company?.subscription" class="mt-1 flex flex-col items-center justify-center gap-1">
                            <span class="text-xs font-bold text-emerald-600">{{ $page.props.auth.user.company.subscription.package?.name || 'Trial' }} Plan</span>
                            <span v-if="$page.props.auth.user.company.subscription.status === 'trial'" class="px-1.5 py-0.5 bg-orange-100 text-orange-600 text-[9px] font-bold rounded uppercase">Trial</span>
                        </div>
                    </div>
                    <Link :href="route('dashboard')" class="block w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-center text-[15px] font-bold rounded-xl transition-colors shadow-sm">Go to Dashboard</Link>
                    <Link v-if="$page.props.auth.user.company?.subscription?.status === 'trial' || $page.props.auth.user.company?.subscription?.plan?.code === 'free_trial'" :href="route('pricing')" class="block w-full py-3 bg-orange-500 hover:bg-orange-600 text-white text-center text-[15px] font-bold rounded-xl transition-colors shadow-sm">Upgrade Package</Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="block w-full py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-center text-[15px] font-bold rounded-xl transition-colors shadow-sm">Sign In</Link>
                    <Link :href="route('register')" class="block w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-center text-[15px] font-bold rounded-xl transition-colors shadow-sm">Start Free Trial</Link>
                </template>
            </div>
        </div>

        <!-- ================= Main Content Area ================= -->
        <!-- Added pt-16 for mobile header, pb-20 for mobile bottom nav -->
        <main class="flex-1 w-full lg:w-auto lg:h-screen lg:overflow-y-auto bg-slate-50 pt-16 lg:pt-0 pb-20 lg:pb-0 relative z-10">
            <div class="h-full">
                <slot />
            </div>
        </main>

        <!-- ================= Mobile Bottom Navbar ================= -->
        <nav class="lg:hidden fixed bottom-0 w-full bg-white border-t border-slate-100 h-[68px] flex items-center justify-around z-50 px-2 pb-safe shadow-[0_-10px_30px_rgba(0,0,0,0.03)]">
            <Link :href="route('home')" class="flex flex-col items-center justify-center w-16 gap-1" :class="!$page.url.includes('track') && !$page.url.includes('login') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600'">
                <svg class="w-[22px] h-[22px]" fill="currentColor" viewBox="0 0 20 20" v-if="!$page.url.includes('track') && !$page.url.includes('login')"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-else><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-bold mt-0.5">Home</span>
            </Link>
            <button class="flex flex-col items-center justify-center w-16 gap-1 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="text-[10px] font-bold mt-0.5">Categories</span>
            </button>
            <Link :href="route('track.order.page')" class="flex flex-col items-center justify-center w-16 gap-1" :class="$page.url.includes('track') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600'">
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="text-[10px] font-bold mt-0.5">Orders</span>
            </Link>
            <Link :href="route('login')" class="flex flex-col items-center justify-center w-16 gap-1" :class="$page.url.includes('login') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600'">
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold mt-0.5">Account</span>
            </Link>
        </nav>

    </div>
</template>
