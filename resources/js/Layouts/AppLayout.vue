<template>
    <div class="min-h-screen bg-gray-100">
        <TopNavbar />

        <!-- Page Heading -->
        <header class="bg-white shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header"></slot>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot></slot>
        </main>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import TopNavbar from '@/Components/TopNavbar.vue';

const page = usePage();
const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);

const permissions = computed(() => page.props.auth.permissions || {});
const userScope = computed(() => page.props.auth.scope);

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
