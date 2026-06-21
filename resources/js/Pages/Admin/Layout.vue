<template>
    <div class="min-h-screen bg-slate-50 flex">

        <!-- ─── Sidebar ────────────────────────────────────────────────────── -->
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 min-h-screen flex flex-col">
            <!-- Logo -->
            <div class="px-5 py-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">MediSaaS</p>
                        <p class="text-[10px] text-emerald-400 font-medium">Platform Control</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-4">

                <!-- Dashboard -->
                <Link :href="route('admin.dashboard')"
                    :class="route().current('admin.dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    <span class="text-base">🏠</span> Dashboard
                </Link>

                <!-- Tenant Management -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Tenant Management</p>
                    <Link :href="route('admin.companies.index')"
                        :class="route().current('admin.companies.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🏢</span> Companies
                    </Link>
                    <Link :href="route('admin.branch-approvals.index')"
                        :class="route().current('admin.branch-approvals.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="flex items-center gap-2.5"><span class="text-base">🏪</span> Branch Approvals</span>
                        <span v-if="pendingBranches > 0" class="bg-orange-500 text-white text-[10px] font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1">{{ pendingBranches }}</span>
                    </Link>
                    <Link :href="route('admin.trial-accounts.index')"
                        :class="route().current('admin.trial-accounts.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🧪</span> Trial Accounts
                    </Link>
                </div>

                <!-- Subscription & Billing -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Subscription & Billing</p>
                    <Link :href="route('admin.packages.index')"
                        :class="route().current('admin.packages.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🗃️</span> Packages
                    </Link>
                    <Link :href="route('admin.subscriptions.index')"
                        :class="route().current('admin.subscriptions.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📦</span> Subscriptions
                    </Link>
                    <Link :href="route('admin.invoices.index')"
                        :class="route().current('admin.invoices.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📄</span> Invoices
                    </Link>
                    <Link :href="route('admin.payments.index')"
                        :class="route().current('admin.payments.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">💳</span> Payments
                    </Link>
                    <Link :href="route('admin.coupons.index')"
                        :class="route().current('admin.coupons.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🏷️</span> Coupons
                    </Link>
                </div>

                <!-- Sales & CRM -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Sales & CRM</p>
                    <Link :href="route('admin.crm.leads.index')"
                        :class="route().current('admin.crm.leads.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🎯</span> Leads
                    </Link>
                    <Link :href="route('admin.demo-requests.index')"
                        :class="route().current('admin.demo-requests.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="flex items-center gap-2.5"><span class="text-base">📩</span> Demo Requests</span>
                        <span v-if="newLeads > 0" class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    </Link>
                    <Link :href="route('admin.crm.trial-funnel.index')"
                        :class="route().current('admin.crm.trial-funnel.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🛤️</span> Trial Funnel
                    </Link>
                </div>

                <!-- Affiliate Management -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Affiliate System</p>
                    <Link :href="route('admin.affiliate.applications')"
                        :class="route().current('admin.affiliate.applications') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📝</span> Applications
                    </Link>
                    <Link :href="route('admin.affiliate.resellers')"
                        :class="route().current('admin.affiliate.resellers*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🤝</span> Resellers
                    </Link>
                    <Link :href="route('admin.affiliate.commissions')"
                        :class="route().current('admin.affiliate.commissions') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">💰</span> Commissions
                    </Link>
                    <Link :href="route('admin.affiliate.withdrawals')"
                        :class="route().current('admin.affiliate.withdrawals') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">💸</span> Withdrawals
                    </Link>
                </div>

                <!-- User Management -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">User Management</p>
                    <Link :href="route('admin.users.index')"
                        :class="route().current('admin.users.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">👥</span> Platform Users
                    </Link>
                    <Link :href="route('admin.roles.index')"
                        :class="route().current('admin.roles.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🎭</span> Roles
                    </Link>
                    <Link :href="route('admin.login-history.index')"
                        :class="route().current('admin.login-history.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">⏱️</span> Login History
                    </Link>
                </div>

                <!-- Support Center -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Support Center</p>
                    <Link :href="route('admin.support-tickets.index')"
                        :class="route().current('admin.support-tickets.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🎫</span> Tickets
                    </Link>
                    <Link :href="route('admin.announcements.index')"
                        :class="route().current('admin.announcements.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📢</span> Announcements
                    </Link>
                </div>

                <!-- Platform Infrastructure -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Platform Infrastructure</p>
                    <Link :href="route('admin.monitoring.index')"
                        :class="route().current('admin.monitoring.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📈</span> Monitoring
                    </Link>
                    <Link :href="route('admin.feature-flags.index')"
                        :class="route().current('admin.feature-flags.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🚀</span> Feature Flags
                    </Link>
                </div>
                
                <!-- Analytics -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Analytics</p>
                    <Link :href="route('admin.analytics.index')"
                        :class="route().current('admin.analytics.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📈</span> Platform Metrics
                    </Link>
                </div>

                <!-- Security -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Security</p>
                    <Link :href="route('admin.security.audit-logs')"
                        :class="route().current('admin.security.audit-logs') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🛡️</span> Audit Logs
                    </Link>
                    <Link :href="route('admin.security.failed-logins')"
                        :class="route().current('admin.security.failed-logins') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🚫</span> Failed Logins
                    </Link>
                </div>

                <!-- Monitoring -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Monitoring</p>
                    <Link :href="route('admin.monitoring.system-health')"
                        :class="route().current('admin.monitoring.system-health') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">🖥️</span> System Health
                    </Link>
                    <Link :href="route('admin.monitoring.queue-monitor')"
                        :class="route().current('admin.monitoring.queue-monitor') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">⚙️</span> Queue Monitor
                    </Link>
                </div>

                <!-- Administration -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 px-3 mb-1">Administration</p>
                    <Link :href="route('admin.settings.index')"
                        :class="route().current('admin.settings.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">⚙️</span> Settings
                    </Link>
                    <Link :href="route('admin.settings.email')"
                        :class="route().current('admin.settings.email') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">📧</span> Email
                    </Link>
                    <Link :href="route('admin.settings.sms')"
                        :class="route().current('admin.settings.sms') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">💬</span> SMS
                    </Link>
                    <Link :href="route('admin.settings.payment-gateway')"
                        :class="route().current('admin.settings.payment-gateway') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="text-base">💳</span> Payment Gateway
                    </Link>
                </div>

            </nav>

            <!-- Bottom: User Info + Logout -->
            <div class="px-4 py-4 border-t border-slate-700/50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-700 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ $page.props.auth?.user?.name?.charAt(0) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ $page.props.auth?.user?.name }}</p>
                        <p class="text-[10px] text-emerald-400">Super Admin</p>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button"
                    class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:bg-slate-800 hover:text-red-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- ─── Main Content ─────────────────────────────────────────────────── -->
        <main class="flex-1 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0 sticky top-0 z-10">
                <h1 class="text-lg font-bold text-slate-800">
                    <slot name="header">Admin</slot>
                </h1>
                <div class="flex items-center gap-3">
                    <Link v-if="pendingBranches > 0" :href="route('admin.branch-approvals.index')"
                        class="flex items-center gap-1.5 bg-orange-50 text-orange-700 border border-orange-200 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-orange-100 transition-colors">
                        🏪 {{ pendingBranches }} pending branch{{ pendingBranches > 1 ? 'es' : '' }}
                    </Link>
                    <Link v-if="newLeads > 0" :href="route('admin.demo-requests.index')"
                        class="flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-blue-100 transition-colors">
                        📩 {{ newLeads }} new lead{{ newLeads > 1 ? 's' : '' }}
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-6 overflow-y-auto">
                <div v-if="$page.props.flash?.success" class="mb-5 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                    ✅ {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-5 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                    ❌ {{ $page.props.flash.error }}
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const pendingBranches = computed(() => page.props.pendingBranches ?? 0);
const newLeads        = computed(() => page.props.newLeads ?? 0);
</script>
