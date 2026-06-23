<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);
</script>

<template>
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 gap-4">
                <div class="flex items-center gap-4 lg:gap-8 flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-500 shadow-sm flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-slate-800">SaaSMedi</span>
                    </div>
                    <div class="hidden sm:flex space-x-4 lg:space-x-8 h-full overflow-x-auto whitespace-nowrap flex-1" style="scrollbar-width: none;">
                        <Link :href="route('dashboard')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url === '/dashboard' ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Dashboard
                        </Link>
                        <Link :href="route('items.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/medicines') || $page.url.startsWith('/items') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Master Data
                        </Link>
                        <Link :href="route('suppliers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/suppliers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Suppliers
                        </Link>
                        <Link :href="route('purchases.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/purchases') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Purchases
                        </Link>
                        <Link :href="route('sales.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/sales') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Sales
                        </Link>
                        <Link :href="route('online-orders.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/online-orders') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Online Orders
                        </Link>
                        <Link :href="route('customers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/customers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Customers
                        </Link>
                        <Link :href="route('reports.expiry')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Reports
                        </Link>
                    </div>
                </div>
                <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
                    <Link :href="route('pos.index')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-3 lg:px-4 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="h-4 w-4 lg:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="hidden xl:inline">Open POS Terminal</span>
                        <span class="hidden lg:inline xl:hidden">POS</span>
                    </Link>

                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                        <div @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 cursor-pointer select-none">
                            <img v-if="$page.props.auth.user?.avatar" :src="'/storage/' + $page.props.auth.user.avatar" class="h-9 w-9 rounded-full object-cover border border-slate-200" alt="Avatar">
                            <div v-else class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                                {{ $page.props.auth.user?.name?.charAt(0) }}
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        
                        <!-- Invisible overlay to close dropdown -->
                        <div v-if="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-40"></div>

                        <!-- Dropdown Menu -->
                        <div v-show="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg shadow-slate-200/50 py-1 border border-slate-100 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 mb-1">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $page.props.auth.user?.name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $page.props.auth.user?.email }}</p>
                            </div>
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Profile
                            </Link>
                            <Link v-if="$page.props.auth.user?.is_company_owner" :href="route('company.settings.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Settings
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition-colors border-t border-slate-100 mt-1 pt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="sm:hidden border-t border-slate-200">
            <div class="pt-2 pb-3 space-y-1">
                <Link :href="route('dashboard')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url === '/dashboard' ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Dashboard
                </Link>
                <Link :href="route('items.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/medicines') || $page.url.startsWith('/items') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Master Data
                </Link>
                <Link :href="route('suppliers.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/suppliers') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Suppliers
                </Link>
                <Link :href="route('purchases.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/purchases') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Purchases
                </Link>
                <Link :href="route('sales.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/sales') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Sales
                </Link>
                <Link :href="route('online-orders.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/online-orders') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Online Orders
                </Link>
                <Link :href="route('customers.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/customers') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Customers
                </Link>
                <Link :href="route('reports.expiry')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-transparent text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300'">
                    Reports
                </Link>
            </div>
        </div>
    </nav>
</template>
