<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);
</script>

<template>
    <nav class="bg-white border-b border-slate-200 flex-shrink-0 z-50 sticky top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 gap-4">
                <div class="flex items-center gap-4 lg:gap-8 flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-shrink-0 sm:hidden">
                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="hidden sm:flex space-x-4 lg:space-x-8 h-full overflow-x-visible whitespace-nowrap flex-1">
                        <Link :href="route('dashboard')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full" :class="$page.url === '/dashboard' ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Dashboard
                        </Link>
                        <Link :href="route('inventory.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full" :class="$page.url.startsWith('/inventory') || $page.url.startsWith('/medicines') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Master Data
                        </Link>
                        
                        <!-- Procurement Dropdown -->
                        <div class="relative inline-flex items-center h-full group">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full border-transparent text-slate-500 group-hover:text-slate-700 group-hover:border-slate-300" :class="$page.url.startsWith('/purchases') || $page.url.startsWith('/purchase-orders') || $page.url.startsWith('/requisitions') || $page.url.startsWith('/suppliers') ? 'border-emerald-500 text-slate-900' : ''">
                                Procurement
                                <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute left-0 top-full mt-1 w-48 rounded-xl bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                <Link :href="route('suppliers.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Suppliers</Link>
                                <Link :href="route('requisitions.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Requisitions</Link>
                                <Link :href="route('purchase-orders.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Purchase Orders</Link>
                                <Link :href="route('purchases.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Purchases (GRN)</Link>
                            </div>
                        </div>

                        <!-- Sales & CRM Dropdown -->
                        <div class="relative inline-flex items-center h-full group">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full border-transparent text-slate-500 group-hover:text-slate-700 group-hover:border-slate-300" :class="$page.url.startsWith('/sales') || $page.url.startsWith('/online-orders') || $page.url.startsWith('/customers') ? 'border-emerald-500 text-slate-900' : ''">
                                Sales & CRM
                                <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute left-0 top-full mt-1 w-48 rounded-xl bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                <Link :href="route('sales.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Sales Invoices</Link>
                                <Link :href="route('online-orders.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Online Orders</Link>
                                <Link :href="route('prescriptions.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Prescriptions</Link>
                                <Link :href="route('customers.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Customers</Link>
                                <div class="border-t border-slate-100 my-1"></div>
                                <Link :href="route('contact-messages.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Storefront Messages</Link>
                            </div>
                        </div>

                        <!-- HR & Payroll Dropdown -->
                        <div class="relative inline-flex items-center h-full group">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full border-transparent text-slate-500 group-hover:text-slate-700 group-hover:border-slate-300" :class="$page.url.startsWith('/hr') ? 'border-emerald-500 text-slate-900' : ''">
                                HR & Payroll
                                <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute left-0 top-full mt-1 w-48 rounded-xl bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                <Link :href="route('hr.employees.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Employees</Link>
                                <Link :href="route('hr.attendance.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Attendance</Link>
                                <Link :href="route('hr.payroll.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Payroll Processing</Link>
                            </div>
                        </div>

                        <!-- Accounting Dropdown -->
                        <div class="relative inline-flex items-center h-full group">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full border-transparent text-slate-500 group-hover:text-slate-700 group-hover:border-slate-300" :class="$page.url.startsWith('/accounting') ? 'border-emerald-500 text-slate-900' : ''">
                                Accounting
                                <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute left-0 top-full mt-1 w-48 rounded-xl bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                <Link :href="route('accounting.transactions.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Income & Expenses</Link>
                                <Link :href="route('accounting.accounts.index')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Chart of Accounts</Link>
                            </div>
                        </div>

                        <Link :href="route('reports.expiry')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors h-full" :class="$page.url.startsWith('/reports') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Reports
                        </Link>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                    <Link :href="route('pos.index')" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 sm:px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-all active:scale-[0.98]">
                        <svg class="-ml-0.5 sm:mr-1.5 h-4 w-4" :class="{'mr-1.5': false, 'mr-1': true}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="hidden sm:inline">Open POS Terminal</span>
                        <span class="sm:hidden ml-1">POS</span>
                    </Link>

                    <!-- Settings Dropdown -->
                    <div class="relative ml-3 h-full flex items-center">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-slate-300 transition-colors">
                            <div class="h-9 w-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-lg border border-emerald-200">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                        </button>
                        
                        <div v-show="dropdownOpen" class="absolute right-0 top-full mt-2 w-56 rounded-2xl bg-white shadow-lg ring-1 ring-slate-900/5 focus:outline-none z-50 overflow-hidden transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <div class="py-1">
                                <Link v-if="$page.props.auth.user.is_company_owner" :href="route('company.settings.index')" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                                    <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Company Settings
                                </Link>
                                <Link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                                    <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile
                                </Link>
                                <div class="border-t border-slate-100 my-1"></div>
                                <Link :href="route('logout')" method="post" as="button" class="flex w-full items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                    <svg class="mr-3 h-4 w-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="sm:hidden border-t border-slate-200 bg-white">
            <div class="px-4 py-3 border-b border-slate-100">
                <Link :href="route('pos.index')" class="flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition-all active:scale-[0.98]">
                    <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Open POS Terminal
                </Link>
            </div>
            <div class="pt-2 pb-3 space-y-1">
                <Link :href="route('dashboard')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url === '/dashboard' ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Dashboard
                </Link>
                <Link :href="route('inventory.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/inventory') || $page.url.startsWith('/medicines') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Master Data
                </Link>
                <div class="block pl-3 pr-4 py-2 font-bold text-slate-400 uppercase text-xs tracking-wider mt-4">Procurement</div>
                <Link :href="route('suppliers.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/suppliers') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Suppliers
                </Link>
                <Link :href="route('requisitions.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/requisitions') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Requisitions
                </Link>
                <Link :href="route('purchase-orders.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/purchase-orders') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Purchase Orders
                </Link>
                <Link :href="route('purchases.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/purchases') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Purchases (GRN)
                </Link>
                <div class="block pl-3 pr-4 py-2 font-bold text-slate-400 uppercase text-xs tracking-wider mt-4">Sales & CRM</div>
                <Link :href="route('sales.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/sales') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Sales Invoices
                </Link>
                <Link :href="route('online-orders.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/online-orders') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Online Orders
                </Link>
                <Link :href="route('prescriptions.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/prescriptions') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Prescriptions
                </Link>
                <Link :href="route('customers.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/customers') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Customers
                </Link>
                <Link :href="route('contact-messages.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/contact-messages') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Storefront Messages
                </Link>
                <div class="block pl-3 pr-4 py-2 font-bold text-slate-400 uppercase text-xs tracking-wider mt-4">HR & Payroll</div>
                <Link :href="route('hr.employees.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/hr/employees') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Employees
                </Link>
                <Link :href="route('hr.attendance.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/hr/attendance') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Attendance
                </Link>
                <Link :href="route('hr.payroll.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/hr/payroll') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Payroll Processing
                </Link>
                <div class="block pl-3 pr-4 py-2 font-bold text-slate-400 uppercase text-xs tracking-wider mt-4">Accounting</div>
                <Link :href="route('accounting.transactions.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/accounting/transactions') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Income & Expenses
                </Link>
                <Link :href="route('accounting.accounts.index')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/accounting/accounts') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Chart of Accounts
                </Link>
                <div class="block pl-3 pr-4 py-2 font-bold text-slate-400 uppercase text-xs tracking-wider mt-4">Other</div>
                <Link :href="route('reports.expiry')" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800'">
                    Reports
                </Link>
            </div>
        </div>
    </nav>
</template>
