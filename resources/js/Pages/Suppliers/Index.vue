<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    suppliers: Object,
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('suppliers.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));
</script>

<template>
    <Head title="Suppliers - SaaSMedi" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Supplier Management</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage your suppliers, distributors, and their ledger balances.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('suppliers.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Supplier
                    </Link>
                </div>
            </div>

            <!-- Flash Message -->
            <div v-if="$page.props.flash.success" class="mb-6 rounded-xl bg-emerald-50 p-4 border border-emerald-200 shadow-sm flex items-start gap-3">
                <svg class="h-5 w-5 text-emerald-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-emerald-800">Success</h3>
                    <div class="mt-1 text-sm text-emerald-700">
                        {{ $page.props.flash.success }}
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow max-w-md group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Search by name, company, or phone..." 
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                    >
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Supplier Details</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Balance</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-inner border border-blue-100">
                                            {{ supplier.name.charAt(0) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-semibold text-slate-900">{{ supplier.name }}</div>
                                            <div class="text-sm text-slate-500">{{ supplier.companies && supplier.companies.length ? supplier.companies.join(', ') : 'Individual' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-sm text-slate-900">{{ supplier.phone }}</div>
                                    <div class="text-sm text-slate-500">{{ supplier.email || '-' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right text-sm font-semibold" :class="parseFloat(supplier.current_balance) > 0 ? 'text-rose-600' : 'text-emerald-600'">
                                    ${{ parseFloat(supplier.current_balance).toFixed(2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-slate-500">
                                    <span v-if="supplier.is_active" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">Inactive</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <Link :href="route('suppliers.edit', supplier.id)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                        Edit<span class="sr-only">, {{ supplier.name }}</span>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="suppliers.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium text-slate-900">No suppliers found</p>
                                    <p class="mt-1">Add your first supplier to start tracking purchases.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="suppliers.links && suppliers.links.length > 3" class="flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-700">
                                Showing <span class="font-medium">{{ suppliers.from }}</span> to <span class="font-medium">{{ suppliers.to }}</span> of <span class="font-medium">{{ suppliers.total }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link 
                                    v-for="(link, index) in suppliers.links" 
                                    :key="index"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-slate-300 focus:z-20 focus:outline-offset-0 transition-colors"
                                    :class="[
                                        link.active ? 'z-10 bg-emerald-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600' : 'text-slate-900 hover:bg-slate-50',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === suppliers.links.length - 1 ? 'rounded-r-md' : '',
                                        !link.url ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
                                    ]"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
