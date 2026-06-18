<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 min-h-screen">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-emerald-400">MediSaaS Admin</h1>
            </div>
            
            <nav class="mt-4 px-4 space-y-2">
                <Link :href="route('admin.dashboard')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Dashboard
                </Link>
                <Link :href="route('admin.companies.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.companies.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Companies
                </Link>
                <Link :href="route('admin.branch-approvals.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.branch-approvals.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Branch Approvals
                </Link>
                <Link :href="route('admin.subscriptions.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.subscriptions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Subscriptions
                </Link>
                <Link :href="route('admin.users.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    All Users
                </Link>
                <Link :href="route('admin.support-tickets.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.support-tickets.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Support Tickets
                </Link>
                <Link :href="route('admin.feature-flags.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.feature-flags.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Feature Flags
                </Link>
                <Link :href="route('admin.settings.index')" class="block px-4 py-2 rounded transition-colors" :class="route().current('admin.settings.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                    Settings
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white border-b px-6 py-4 flex justify-between items-center shrink-0">
                <h2 class="text-xl font-semibold text-gray-800">
                    <slot name="header"></slot>
                </h2>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ $page.props.auth?.user?.name }} (Super Admin)</span>
                    <Link :href="route('logout')" method="post" as="button" class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Log Out
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-6 overflow-y-auto">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>
                
                <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.error }}
                </div>

                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
