<script setup>
import { ref, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const form = useForm({
    items: []
});

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;
const searchInputRef = ref(null);

const searchMedicines = () => {
    clearTimeout(searchTimeout);
    
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        isSearching.value = true;
        try {
            const response = await axios.get(route('inventory.opening-stock.search'), {
                params: { q: searchQuery.value }
            });
            searchResults.value = response.data;

            // Auto-select if there's an exact match (e.g. barcode scan)
            if (searchResults.value.length === 1 && searchQuery.value.length > 5) {
                addItem(searchResults.value[0]);
            }
        } catch (error) {
            console.error('Error searching medicines:', error);
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const addItem = (item) => {
    // Generate a default batch number
    const defaultBatch = 'OB-' + Math.random().toString(36).substring(2, 8).toUpperCase();
    
    // Default expiry 1 year from now
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    const defaultExpiry = d.toISOString().split('T')[0];

    form.items.unshift({
        id: item.id,
        name: item.name,
        generic_name: item.generic_name,
        uom: item.uom,
        batch_no: defaultBatch,
        expiry_date: defaultExpiry,
        quantity: 1,
        mrp: item.mrp,
        tp: item.tp
    });
    
    searchQuery.value = '';
    searchResults.value = [];
    
    // Keep focus on input for continuous scanning
    if (searchInputRef.value) {
        searchInputRef.value.focus();
    }
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('inventory.opening-stock.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            searchQuery.value = '';
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Opening stock saved successfully.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Opening Stock / Physical Count" />

        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Physical Inventory / Opening Stock
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    
                    <!-- Search Panel -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">
                                Scan & Add
                            </h3>
                            
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input 
                                    ref="searchInputRef"
                                    v-model="searchQuery" 
                                    @input="searchMedicines" 
                                    type="text" 
                                    autofocus
                                    class="block w-full rounded-xl border-slate-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-3" 
                                    placeholder="Scan barcode or type name...">
                                <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-emerald-500"></div>
                                </div>
                            </div>

                            <p class="text-xs text-slate-500 mt-2">Use a barcode scanner to rapidly build your opening stock list.</p>

                            <!-- Search Results Dropdown -->
                            <div v-if="searchResults.length > 0 && searchQuery" class="absolute z-10 mt-2 w-full bg-white shadow-2xl rounded-xl border border-slate-100 py-1 max-h-96 overflow-auto left-0">
                                <div v-for="item in searchResults" :key="item.id" @click="addItem(item)" class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0 flex justify-between items-center transition-colors">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ item.name }}</div>
                                        <div class="text-xs text-slate-500" v-if="item.generic_name">{{ item.generic_name }}</div>
                                        <div class="text-xs font-medium text-emerald-600 mt-1">MRP: ${{ item.mrp }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                            <h4 class="font-bold text-indigo-800 text-sm mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Pro Tips
                            </h4>
                            <ul class="text-xs text-indigo-700 space-y-2 list-disc pl-4">
                                <li>All entries will be logged as 'Opening Stock' instead of Purchases, keeping financial reports accurate.</li>
                                <li>Pressing ENTER on your scanner auto-selects exact matches.</li>
                                <li>The Trade Price (TP) automatically defaults to MRP - 12%. Adjust it if needed.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Selected Items Table -->
                    <div class="lg:col-span-3">
                        <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-slate-800">Scanned Items ({{ form.items.length }})</h3>
                                
                                <button type="submit" :disabled="form.processing || form.items.length === 0" class="inline-flex justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                                    {{ form.processing ? 'Saving...' : 'Save Opening Stock' }}
                                </button>
                            </div>
                            
                            <div class="overflow-x-auto min-h-[400px]">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Item</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-28">Batch</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-36">Expiry</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">Qty</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">TP ($)</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">MRP ($)</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase w-12"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="(item, index) in form.items" :key="index" class="bg-white hover:bg-slate-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-bold text-slate-900">{{ item.name }}</div>
                                                <div class="text-xs text-slate-500">Unit: {{ item.uom }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="text" v-model="item.batch_no" class="block w-full rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2" required>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date" v-model="item.expiry_date" class="block w-full rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2" required>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number" v-model="item.quantity" min="1" class="block w-full rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2" required>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number" step="0.01" v-model="item.tp" min="0" class="block w-full rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2" required>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number" step="0.01" v-model="item.mrp" min="0" class="block w-full rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2" required>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                <button type="button" @click="removeItem(index)" class="text-rose-400 hover:text-rose-600 p-1 rounded hover:bg-rose-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="form.items.length === 0">
                                            <td colspan="7" class="px-4 py-16 text-center text-slate-500">
                                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                                <p class="text-sm font-medium">No items scanned yet.</p>
                                                <p class="text-xs mt-1">Start scanning barcodes or typing medicine names to build your inventory.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
