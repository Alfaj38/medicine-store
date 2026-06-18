<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <span class="text-xl font-bold text-indigo-600">MediSaaS ERP</span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <template v-for="item in navItems" :key="item.key">
                                <a :href="item.route" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                   :class="[item.active ? 'border-indigo-500 text-gray-900 focus:outline-none focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300']">
                                    <span class="mr-2">{{ item.icon }}</span>
                                    {{ item.label }}
                                </a>
                            </template>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Scope Badge -->
                        <div class="mr-4">
                            <span v-if="scopeBadge" :class="scopeBadge.class" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                {{ scopeBadge.text }}
                            </span>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-3 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user?.name }}</span>
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-800 font-bold">
                                    {{ $page.props.auth.user?.name?.charAt(0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

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
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const permissions = computed(() => page.props.auth.permissions || {});
const userScope = computed(() => page.props.auth.scope);

const navItems = computed(() => {
    const allItems = [
        { key: 'dashboard',  label: 'Dashboard',  icon: '📊', route: '/dashboard', always: true },
        { key: 'pos',        label: 'POS',        icon: '🛒', route: '/pos', perm: 'pos' },
        { key: 'purchases',  label: 'Purchases',  icon: '📦', route: '/purchases', perm: 'purchases' },
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
