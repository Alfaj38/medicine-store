<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xl shadow-inner">
                    {{ company.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 leading-none mb-1">Tenant 360°: {{ company.name }}</h2>
                    <div class="flex items-center gap-2 text-xs font-medium">
                        <span v-if="company.registration_status === 'active'" class="text-emerald-600 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active Tenant</span>
                        <span v-else-if="company.registration_status === 'pending'" class="text-amber-600 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending Approval</span>
                        <span v-else-if="company.registration_status === 'suspended'" class="text-red-600 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Suspended</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-slate-500">Joined {{ new Date(company.created_at).toLocaleDateString() }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            
            <!-- Quick Actions Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-2 border border-slate-200 rounded-2xl shadow-sm">
                <div class="flex items-center gap-2 pl-2">
                    <span class="text-sm font-semibold text-slate-600">Quick Actions:</span>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Impersonate Button -->
                    <Link :href="route('admin.companies.impersonate', company.id)" method="post" as="button" 
                        class="flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <span class="text-base">👤</span> Login as Tenant
                    </Link>

                    <div class="w-px h-6 bg-slate-200 mx-1"></div>

                    <!-- Status Actions -->
                    <Link v-if="company.registration_status === 'pending'" :href="route('admin.companies.approve', company.id)" method="post" as="button" 
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Approve Tenant
                    </Link>
                    <Link v-if="company.registration_status === 'active'" :href="route('admin.companies.suspend', company.id)" method="post" as="button" 
                        class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200 text-sm font-semibold rounded-xl transition-colors">
                        Suspend Account
                    </Link>
                    <Link v-if="company.registration_status === 'suspended'" :href="route('admin.companies.approve', company.id)" method="post" as="button" 
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Unsuspend Account
                    </Link>
                </div>
            </div>

            <!-- Health & Metrics KPI Grid -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Lifetime Value</p>
                    <h3 class="text-2xl font-black text-slate-800">৳{{ stats.total_revenue }}</h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Branches</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ stats.total_branches }}</h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Active Users</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ stats.total_users }}</h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Open Tickets</p>
                    <h3 class="text-2xl font-black" :class="stats.open_tickets > 0 ? 'text-orange-500' : 'text-emerald-500'">{{ stats.open_tickets }}</h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Last Login</p>
                    <h3 class="text-sm font-bold text-slate-800">{{ stats.last_login ? new Date(stats.last_login).toLocaleDateString() : 'Never' }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Subscription & Billing Panel -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2"><span class="text-lg">💳</span> Subscription & Billing</h3>
                            <span v-if="company.subscription?.status === 'active'" class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-full">Active Subscription</span>
                            <span v-else class="px-2.5 py-1 bg-slate-200 text-slate-600 font-bold text-xs rounded-full">No Active Subscription</span>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                <div>
                                    <p class="text-xs text-slate-500 font-medium mb-1">Current Plan</p>
                                    <p class="font-bold text-slate-800 capitalize">{{ company.subscription?.package?.name || company.subscription_plan || 'Free / Trial' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-medium mb-1">Billing Cycle</p>
                                    <p class="font-bold text-slate-800 capitalize">{{ company.subscription?.billing_cycle || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-medium mb-1">Start Date</p>
                                    <p class="font-bold text-slate-800">{{ company.subscription?.starts_at ? new Date(company.subscription.starts_at).toLocaleDateString() : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-medium mb-1">Next Billing / Expiry</p>
                                    <p class="font-bold text-slate-800">{{ company.subscription?.ends_at ? new Date(company.subscription.ends_at).toLocaleDateString() : 'N/A' }}</p>
                                </div>
                            </div>

                            <h4 class="text-sm font-bold text-slate-800 mb-3 border-t border-slate-100 pt-4">Recent Payments</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-50 rounded-lg">
                                        <tr>
                                            <th class="p-3 rounded-l-lg">Date</th>
                                            <th class="p-3">Amount</th>
                                            <th class="p-3">Method</th>
                                            <th class="p-3 rounded-r-lg">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="payment in payments" :key="payment.id" class="hover:bg-slate-50/50">
                                            <td class="p-3 font-medium text-slate-700">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                                            <td class="p-3 font-bold text-slate-800">৳{{ payment.net_amount }}</td>
                                            <td class="p-3 text-slate-600 capitalize">{{ payment.gateway }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-xs font-bold"
                                                    :class="payment.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                                    {{ payment.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="!payments.length">
                                            <td colspan="4" class="p-4 text-center text-slate-500 font-medium">No payment history found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Branches Management Panel -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2"><span class="text-lg">🏪</span> Operating Branches</h3>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-left text-sm">
                                <thead class="text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-50/80">
                                    <tr>
                                        <th class="p-4 border-b border-slate-100">Branch Name</th>
                                        <th class="p-4 border-b border-slate-100">Location</th>
                                        <th class="p-4 border-b border-slate-100">Phone</th>
                                        <th class="p-4 border-b border-slate-100">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="branch in company.branches" :key="branch.id" class="hover:bg-slate-50/50 transition-colors">
                                        <td class="p-4">
                                            <div class="font-bold text-slate-800 flex items-center gap-2">
                                                {{ branch.name }}
                                                <span v-if="branch.is_default" class="text-[10px] bg-indigo-100 text-indigo-700 font-bold px-1.5 py-0.5 rounded">HQ</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-slate-600">{{ branch.address || 'N/A' }}</td>
                                        <td class="p-4 text-slate-600">{{ branch.phone || 'N/A' }}</td>
                                        <td class="p-4">
                                            <span v-if="branch.approval_status === 'approved'" class="text-emerald-600 font-bold text-xs bg-emerald-50 px-2 py-1 rounded">Approved</span>
                                            <span v-else-if="branch.approval_status === 'pending'" class="text-amber-600 font-bold text-xs bg-amber-50 px-2 py-1 rounded">Pending</span>
                                            <span v-else class="text-red-600 font-bold text-xs bg-red-50 px-2 py-1 rounded">Rejected</span>
                                        </td>
                                    </tr>
                                    <tr v-if="!company.branches.length">
                                        <td colspan="4" class="p-6 text-center text-slate-500 font-medium">No branches registered.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Side Panel Column -->
                <div class="space-y-6">
                    
                    <!-- Company Profile -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-4 pb-3 border-b border-slate-100"><span class="text-lg">🏢</span> Company Profile</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-0.5">Primary Email</p>
                                <p class="text-sm font-semibold text-slate-800 break-all">{{ company.email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-0.5">Primary Phone</p>
                                <p class="text-sm font-semibold text-slate-800">{{ company.phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-0.5">Address</p>
                                <p class="text-sm font-semibold text-slate-800">{{ company.address || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Details -->
                    <div class="bg-slate-900 rounded-2xl shadow-sm p-5 text-white">
                        <h3 class="text-base font-bold text-white flex items-center gap-2 mb-4 pb-3 border-b border-slate-700"><span class="text-lg">👑</span> Primary Owner</h3>
                        <div v-if="company.users.find(u => u.is_company_owner)" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-slate-300">
                                    {{ company.users.find(u => u.is_company_owner).name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ company.users.find(u => u.is_company_owner).name }}</p>
                                    <p class="text-xs text-slate-400">{{ company.users.find(u => u.is_company_owner).email }}</p>
                                </div>
                            </div>
                            <div class="pt-2">
                                <p class="text-xs text-slate-400 font-medium mb-0.5">Phone Number</p>
                                <p class="text-sm font-semibold">{{ company.users.find(u => u.is_company_owner).phone || 'N/A' }}</p>
                            </div>
                        </div>
                        <div v-else class="text-sm text-slate-400 italic">No designated owner found.</div>
                    </div>

                    <!-- Recent Support Tickets -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2"><span class="text-lg">🎫</span> Support Tickets</h3>
                            <Link :href="route('admin.support-tickets.index')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">View All</Link>
                        </div>
                        <div class="p-0">
                            <div v-if="tickets.length > 0" class="divide-y divide-slate-100">
                                <div v-for="ticket in tickets" :key="ticket.id" class="p-4 hover:bg-slate-50 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-sm font-bold text-slate-800 line-clamp-1 flex-1 pr-2">{{ ticket.subject }}</p>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0"
                                            :class="ticket.status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600'">
                                            {{ ticket.status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">{{ new Date(ticket.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            <div v-else class="p-6 text-center text-sm font-medium text-slate-500">
                                No recent tickets.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

defineProps({
    company: Object,
    stats: Object,
    tickets: Array,
    payments: Array
});
</script>
