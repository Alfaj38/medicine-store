<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';

const props = defineProps({
    inventory: Object,
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('inventory.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Array of expanded medicine IDs to show batches
const expandedRows = ref([]);

const toggleRow = (id) => {
    const index = expandedRows.value.indexOf(id);
    if (index === -1) {
        expandedRows.value.push(id);
    } else {
        expandedRows.value.splice(index, 1);
    }
};

const page = usePage();

const showSuccessAlert = () => {
    if (page.props.flash?.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: page.props.flash.success,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }
};

onMounted(() => {
    showSuccessAlert();
});

watch(() => page.props.flash?.success, (newVal) => {
    if (newVal) {
        showSuccessAlert();
    }
});

// Helper to determine row color based on stock
const getStockClass = (qty) => {
    if (qty <= 0) return 'text-rose-600 bg-rose-50 border border-rose-200';
    if (qty <= 10) return 'text-amber-600 bg-amber-50 border border-amber-200';
    return 'text-emerald-700 bg-emerald-50 border border-emerald-100';
};
</script>

<template>
    <Head title="Physical Inventory - SaaSMedi" />

    <AppLayout>
        <main class="w-full mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Physical Inventory</h1>
                    <p class="mt-2 text-sm text-slate-500">Live view of your current physical stock quantities, batches, and values.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('inventory.opening-stock')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Opening Stock
                    </Link>
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
                        placeholder="Search medicines by name or generic name..." 
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
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10"></th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-2">Medicine Info</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Quantity</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg TP</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg MRP</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <template v-for="item in inventory.data" :key="item.medicine_id">
                                <!-- Top Level Row -->
                                <tr @click="toggleRow(item.medicine_id)" class="hover:bg-slate-50 transition-colors cursor-pointer" :class="{'bg-slate-50': expandedRows.includes(item.medicine_id)}">
                                    <td class="pl-4 py-4">
                                        <svg class="h-5 w-5 text-slate-400 transition-transform" :class="{'rotate-90 text-emerald-500': expandedRows.includes(item.medicine_id)}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </td>
                                    <td class="whitespace-nowrap py-4 pl-2 pr-3">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-inner border border-emerald-100">
                                                {{ item.medicine_name.charAt(0) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-semibold text-slate-900">{{ item.medicine_name }}</div>
                                                <div class="text-xs text-slate-500">{{ item.generic_name || 'Unit: ' + item.unit }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-sm font-bold" :class="getStockClass(item.total_quantity)">
                                            {{ item.total_quantity }} {{ item.unit }}s
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-600">
                                        ${{ item.avg_tp.toFixed(2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-600">
                                        ${{ item.avg_mrp.toFixed(2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-slate-800">
                                        ${{ item.total_value.toFixed(2) }}
                                    </td>
                                </tr>
                                
                                <!-- Batches Expanded Row -->
                                <tr v-if="expandedRows.includes(item.medicine_id)" class="bg-slate-50/50">
                                    <td colspan="6" class="px-8 py-4 border-l-4 border-emerald-500">
                                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                                            <table class="min-w-full divide-y divide-slate-100">
                                                <thead class="bg-slate-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Batch No</th>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Expiry Date</th>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Quantity</th>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">TP</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    <tr v-for="batch in item.batches" :key="batch.id">
                                                        <td class="px-4 py-2 text-sm text-slate-700 font-mono">{{ batch.batch_no || 'N/A' }}</td>
                                                        <td class="px-4 py-2 text-sm text-slate-700">{{ batch.expiry_date || 'N/A' }}</td>
                                                        <td class="px-4 py-2 text-sm font-medium text-slate-900">{{ batch.quantity }}</td>
                                                        <td class="px-4 py-2 text-sm text-slate-700">${{ parseFloat(batch.tp).toFixed(2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            
                            <tr v-if="inventory.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium text-slate-900">No inventory found</p>
                                    <p class="mt-1">You haven't added any physical stock yet. Use the Opening Stock tool to begin.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="inventory.links && inventory.links.length > 3" class="flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-700">
                                Showing <span class="font-medium">{{ inventory.from }}</span> to <span class="font-medium">{{ inventory.to }}</span> of <span class="font-medium">{{ inventory.total }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, index) in inventory.links" :key="index">
                                    <Link 
                                        v-if="link.url"
                                        :href="link.url"
                                        v-html="link.label"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-slate-300 focus:z-20 focus:outline-offset-0 transition-colors"
                                        :class="[
                                            link.active ? 'z-10 bg-emerald-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600' : 'text-slate-900 hover:bg-slate-50',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === inventory.links.length - 1 ? 'rounded-r-md' : ''
                                        ]"
                                    />
                                    <span 
                                        v-else
                                        v-html="link.label"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-slate-300 transition-colors opacity-50 cursor-not-allowed pointer-events-none text-slate-900 bg-slate-50"
                                        :class="[
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === inventory.links.length - 1 ? 'rounded-r-md' : ''
                                        ]"
                                    />
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
