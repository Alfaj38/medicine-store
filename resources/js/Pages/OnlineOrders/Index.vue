<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TopNavbar from '@/Components/TopNavbar.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

watch([search, status], debounce(([newSearch, newStatus]) => {
    router.get(route('online-orders.index'), {
        search: newSearch,
        status: newStatus,
    }, { preserveState: true, preserveScroll: true, replace: true });
}, 300));

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    accepted: 'bg-blue-100 text-blue-800',
    processing: 'bg-purple-100 text-purple-800',
    out_for_delivery: 'bg-orange-100 text-orange-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const formatStatus = (status) => {
    return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};
</script>

<template>
    <Head title="Online Orders - SaaSMedi" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Online Orders</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage e-commerce orders from your storefront.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input v-model="search" type="text" placeholder="Search by ID, Name or Phone..." class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                </div>
                <div class="w-full sm:w-48">
                    <select v-model="status" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="processing">Processing</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Order ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-bold text-slate-900 sm:pl-6">
                                    {{ order.tracking_number }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    {{ new Date(order.created_at).toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-900">
                                    <div class="font-medium">{{ order.customer_name }}</div>
                                    <div class="text-slate-500 text-xs">{{ order.customer_phone }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-slate-900">
                                    ৳{{ order.total_amount }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span :class="[statusColors[order.status], 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold']">
                                        {{ formatStatus(order.status) }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <Link :href="route('online-orders.show', order.id)" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors inline-block">
                                        Manage
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    No orders found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="orders.links.length > 3" class="bg-white px-4 py-3 border-t border-slate-200 sm:px-6 flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">Previous</Link>
                        <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">Next</Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-700">
                                Showing <span class="font-medium">{{ orders.from }}</span> to <span class="font-medium">{{ orders.to }}</span> of <span class="font-medium">{{ orders.total }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, i) in orders.links" :key="i">
                                    <div v-if="link.url === null" class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-500" v-html="link.label"></div>
                                    <Link v-else :href="link.url" :class="[link.active ? 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50', 'relative inline-flex items-center px-4 py-2 border text-sm font-medium']" v-html="link.label"></Link>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
