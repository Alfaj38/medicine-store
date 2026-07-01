<template>
    <div class="flex h-screen bg-slate-50 font-sans overflow-hidden">
        <!-- Sidebar Navigation -->
        <Sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            <!-- Top Navigation -->
            <TopNavbar />

            <!-- Page Heading -->
            <header class="bg-white border-b border-slate-200" v-if="$slots.header">
                <div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 relative">
                <!-- Subscription Lifecycle Banners -->
                <div v-if="subscription && subscription.state !== 'active'" class="border-b px-4 py-3 sm:px-6 lg:px-8" :class="{
                    'bg-yellow-50 border-yellow-200': subscription.state === 'renewal_window',
                    'bg-orange-50 border-orange-200': subscription.state === 'grace_period',
                    'bg-blue-50 border-blue-200': subscription.state === 'payment_pending',
                    'bg-red-50 border-red-200': ['restricted', 'suspended'].includes(subscription.state)
                }">
                    <div class="flex items-center justify-between flex-wrap">
                        <div class="flex-1 flex items-center">
                            <span class="flex p-2 rounded-lg mr-3" :class="{
                                'bg-yellow-100 text-yellow-600': subscription.state === 'renewal_window',
                                'bg-orange-100 text-orange-600': subscription.state === 'grace_period',
                                'bg-blue-100 text-blue-600': subscription.state === 'payment_pending',
                                'bg-red-100 text-red-600': ['restricted', 'suspended'].includes(subscription.state)
                            }">
                                <!-- Info Icon -->
                                <svg v-if="['renewal_window', 'payment_pending'].includes(subscription.state)" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <!-- Warning Icon -->
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <p class="font-medium text-sm" :class="{
                                'text-yellow-800': subscription.state === 'renewal_window',
                                'text-orange-800': subscription.state === 'grace_period',
                                'text-blue-800': subscription.state === 'payment_pending',
                                'text-red-800': ['restricted', 'suspended'].includes(subscription.state)
                            }">
                                <template v-if="subscription.state === 'renewal_window'">
                                    Your subscription expires in {{ subscription.days_to_expiry }} days. Renew now to avoid interruption.
                                </template>
                                <template v-else-if="subscription.state === 'grace_period'">
                                    <span class="font-bold">⚠ Your subscription has expired.</span> Grace Period Remaining: {{ subscription.grace_days_remaining }} Days.
                                </template>
                                <template v-else-if="subscription.state === 'payment_pending'">
                                    Payment Pending Verification. Your account remains active while we verify your transaction.
                                </template>
                                <template v-else>
                                    Subscription Expired. Your account is in Restricted Mode. Renew to continue business operations.
                                </template>
                            </p>
                        </div>
                        <div class="mt-2 flex-shrink-0 sm:mt-0 sm:ml-3" v-if="subscription.state !== 'payment_pending'">
                            <Link :href="route('company.subscription.index')" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white transition-colors" :class="{
                                'bg-yellow-600 hover:bg-yellow-700': subscription.state === 'renewal_window',
                                'bg-orange-600 hover:bg-orange-700': subscription.state === 'grace_period',
                                'bg-red-600 hover:bg-red-700': ['restricted', 'suspended'].includes(subscription.state)
                            }">
                                {{ subscription.state === 'renewal_window' ? 'Renew Now' : 'Upgrade Now' }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="py-6">
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import TopNavbar from '@/Components/TopNavbar.vue';
import Sidebar from '@/Components/Sidebar.vue';

const page = usePage();
const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);

const permissions = computed(() => page.props.auth.permissions || {});
const userScope = computed(() => page.props.auth.scope);
const subscription = computed(() => page.props.auth.subscription || null);

const navItems = computed(() => {
    const allItems = [
        { key: 'dashboard',  label: 'Dashboard',  icon: '📊', route: '/dashboard', always: true },
        { key: 'pos',        label: 'POS',        icon: '🛒', route: '/pos', perm: 'pos' },
        { key: 'purchases',  label: 'Purchases (GRN)',  icon: '📦', route: '/purchases', perm: 'purchases' },
        { key: 'requisitions',label: 'Requisitions',icon: '📝', route: '/requisitions', perm: 'purchases' },
        { key: 'purchase-orders',label: 'Purchase Orders',icon: '📄', route: '/purchase-orders', perm: 'purchases' },
        { key: 'medicines',  label: 'Medicines',  icon: '💊', route: '/medicines', perm: 'medicines' },
        { key: 'suppliers',  label: 'Suppliers',  icon: '🚚', route: '/suppliers', perm: 'suppliers' },
        { key: 'customers',  label: 'Customers',  icon: '👥', route: '/customers', perm: 'customers' },
        { key: 'transfers',  label: 'Transfers',  icon: '🔄', route: '/branch-transfers', perm: 'transfers' },
        { key: 'reports',    label: 'Reports',    icon: '📈', route: '/reports', perm: 'reports' },
        { key: 'expenses',   label: 'Expenses',   icon: '💸', route: '/expenses', perm: 'expenses' },
    ];
    
    return allItems.filter(item => {
        if (item.always) return true;
        
        // If they are company owner or platform admin, they see everything
        if (page.props.auth.user?.is_company_owner || ['platform', 'readonly_platform'].includes(userScope.value)) {
            return true;
        }

        // Otherwise check permissions
        return permissions.value[item.perm]?.can_view;
    }).map(item => ({
        ...item,
        active: window.location.pathname.startsWith(item.route)
    }));
});

const scopeBadge = computed(() => {
    const scope = userScope.value;
    
    if (!scope) return null;

    const badges = {
        'platform': { text: 'Platform Admin', class: 'bg-purple-100 text-purple-800' },
        'readonly_platform': { text: 'Platform (Read-Only)', class: 'bg-gray-100 text-gray-800' },
        'company': { text: 'Company Scope', class: 'bg-indigo-100 text-indigo-800' },
        'area': { text: 'Area Scope', class: 'bg-yellow-100 text-yellow-800' },
        'branch': { text: 'Branch Scope', class: 'bg-green-100 text-green-800' },
        'own': { text: 'Own Scope', class: 'bg-red-100 text-red-800' },
    };

    return badges[scope] || null;
});
</script>
