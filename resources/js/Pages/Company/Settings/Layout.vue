<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head :title="title ? `${title} - Administration` : 'Administration'" />

    <div class="min-h-screen bg-slate-50 flex font-sans">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between hidden md:flex">
            <div>
                <!-- Logo -->
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-slate-900 block leading-tight">Pharma Point</span>
                            <span class="text-[10px] text-slate-500 block leading-tight">Management System</span>
                        </div>
                    </div>
                </div>

                <!-- Nav Links -->
                <div class="px-4 py-6">
                    <h3 class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Administration</h3>
                    <nav class="space-y-1">
                        <Link :href="route('dashboard')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </Link>
                        <Link :href="route('company.settings.index')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url === '/company/settings' || $page.url.includes('tab=general') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5" :class="$page.url === '/company/settings' || $page.url.includes('tab=general') ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Company Profile
                        </Link>
                        <Link :href="route('company.branches.index')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/company/branches') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5" :class="$page.url.startsWith('/company/branches') ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Branches
                        </Link>
                        <Link :href="route('company.users.index')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/company/users') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5" :class="$page.url.startsWith('/company/users') ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Employees
                        </Link>
                        <!-- Placeholders for design match -->
                        <Link :href="route('company.roles.index')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/company/roles') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5" :class="$page.url.startsWith('/company/roles') ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Roles & Permissions
                        </Link>

                        <Link :href="route('company.settings.index')" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url === '/company/settings' && !$page.url.includes('tab=') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                            <svg class="mr-3 flex-shrink-0 h-5 w-5" :class="$page.url === '/company/settings' && !$page.url.includes('tab=') ? 'text-emerald-600' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Settings
                        </Link>
                    </nav>
                </div>
            </div>

            <!-- Need Help -->
            <div class="p-4">
                <div class="bg-emerald-50 rounded-xl p-4 text-center border border-emerald-100">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto mb-2 font-bold">?</div>
                    <h4 class="text-sm font-semibold text-emerald-900 mb-1">Need Help?</h4>
                    <p class="text-xs text-emerald-700 mb-3">If you need any assistance, please contact our support team.</p>
                    <button class="w-full bg-white border border-emerald-200 text-emerald-700 text-sm font-medium py-2 rounded-lg hover:bg-emerald-50 transition-colors shadow-sm">
                        Contact Support
                    </button>
                </div>
                <div class="text-center mt-4 text-[10px] text-slate-400">
                    &copy; {{ new Date().getFullYear() }} Pharma Point<br>All rights reserved.
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 overflow-auto">
            <!-- Mobile Header -->
            <div class="md:hidden bg-white border-b border-slate-200 h-16 flex items-center px-4 justify-between">
                <span class="font-bold text-lg text-slate-900">Pharma Point</span>
                <Link :href="route('dashboard')" class="text-sm text-emerald-600 font-medium">Back to Dashboard</Link>
            </div>
            
            <main class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
</template>
