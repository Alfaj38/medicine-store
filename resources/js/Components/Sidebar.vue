<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const permissions = computed(() => page.props.auth?.permissions || {});
const userScope = computed(() => page.props.auth?.scope);

const navItems = computed(() => {
    const allItems = [
        { name: 'Dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', route: 'dashboard', perm: 'dashboard' },
        { name: 'Analytics', icon: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z', route: 'reports.expiry', perm: 'reports-analytics' },
        { name: 'Sales', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', route: 'sales.index', perm: 'sales-management' },
        { name: 'Purchase', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', route: 'purchases.index', perm: 'purchase-management' },
        { name: 'Inventory', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', route: 'inventory.index', perm: 'inventory' },
        { name: 'Opening Stock', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', route: 'inventory.opening-stock', perm: 'inventory' },
        { name: 'Customers', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', route: 'customers.index', perm: 'customers' },
        { name: 'Suppliers', icon: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', route: 'suppliers.index', perm: 'suppliers' },
        { name: 'Expenses', icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z', route: 'reports.expiry', perm: 'expenses' },
        { name: 'Reports', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'reports.expiry', perm: 'reports-analytics' },
        { name: 'Settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', route: 'dashboard', perm: 'settings' },
    ];
    
    return allItems.filter(item => {
        // If they are company owner or platform admin, they see everything
        if (page.props.auth.user?.is_company_owner || ['platform', 'readonly_platform'].includes(userScope.value)) {
            return true;
        }

        // Check if the user has the 'view' permission for this module
        return Array.isArray(permissions.value) && permissions.value.includes(`${item.perm}.view`);
    });
});
</script>

<template>
    <div class="hidden md:flex flex-col w-64 bg-white border-r border-slate-200 h-screen overflow-y-auto z-20">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 h-16 flex-shrink-0 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-emerald-600 shadow-sm flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
            <span class="font-bold text-xl tracking-tight text-slate-800">SaaSMedi</span>
        </div>

        <!-- Menu -->
        <div class="flex-1 py-6 px-4 space-y-1">
            <Link 
                v-for="item in navItems" 
                :key="item.name" 
                :href="route(item.route)" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium transition-colors"
                :class="$page.url.startsWith('/' + item.route.split('.')[0]) || ($page.url === '/dashboard' && item.name === 'Dashboard') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                </svg>
                <span class="text-sm">{{ item.name }}</span>
            </Link>
        </div>

        <div class="mt-auto px-4 pb-2">
            <Link 
                :href="route('logout')" 
                method="post" 
                as="button" 
                class="flex w-full items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-rose-600 hover:bg-rose-50 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="text-sm">Log Out</span>
            </Link>
        </div>

        <!-- Upgrade Box -->
        <div class="p-4 flex-shrink-0">
            <div class="bg-emerald-50 rounded-2xl p-5 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 rounded-full bg-emerald-100 opacity-50 blur-xl"></div>
                <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-16 h-16 rounded-full bg-emerald-200 opacity-40 blur-lg"></div>
                
                <div class="relative z-10">
                    <svg class="w-8 h-8 text-emerald-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <h4 class="font-bold text-slate-800 text-sm mb-1">Upgrade to Premium</h4>
                    <p class="text-xs text-slate-600 mb-4 leading-relaxed">Unlock advanced reports, insights and more features.</p>
                    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2 px-4 rounded-xl shadow-sm transition-all shadow-emerald-500/20 active:scale-[0.98]">
                        Upgrade Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
