<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    suppliers: Array,
});

const form = useForm({
    type: 'Purchase',
    supplier_id: '',
    priority: 'Normal',
    required_date: '',
    notes: '',
    items: [],
});

const isSearching = ref(false);
const searchResults = ref([]);
const searchQuery = ref('');

// Auto Generate Requisition logic
const isGenerating = ref(false);
const generateAutoRequisition = async () => {
    isGenerating.value = true;
    try {
        const response = await axios.get(route('requisitions.auto-generate'));
        const suggestions = response.data;
        
        if (suggestions.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Items Needed',
                text: 'All stock levels are currently above their reorder thresholds.',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        // Merge suggestions into form, avoiding duplicates
        suggestions.forEach(suggest => {
            const exists = form.items.find(i => i.item_id === suggest.item_id);
            if (!exists) {
                form.items.push({
                    item_id: suggest.item_id,
                    name: suggest.name,
                    uom: suggest.uom,
                    current_stock: suggest.current_stock,
                    requested_qty: suggest.suggested_qty,
                    remarks: 'Auto-Suggested'
                });
            }
        });

        form.type = 'Auto';
        
        Swal.fire({
            icon: 'success',
            title: 'Auto-Requisition Generated',
            text: `Added ${suggestions.length} items that are below reorder levels.`,
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to generate auto-requisition.',
            confirmButtonColor: '#10b981'
        });
    } finally {
        isGenerating.value = false;
    }
};

const searchMedicines = debounce(async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get(route('requisitions.search'), {
            params: { 
                q: searchQuery.value,
                supplier_id: form.type === 'Purchase' ? form.supplier_id : ''
            }
        });
        searchResults.value = response.data;
    } catch (error) {
        console.error("Search failed:", error);
    } finally {
        isSearching.value = false;
    }
}, 300);

const addItem = (item) => {
    const exists = form.items.find(i => i.item_id === item.id);
    if (exists) {
        Swal.fire({ icon: 'warning', title: 'Item already added', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        return;
    }

    form.items.push({
        item_id: item.id,
        name: item.name,
        uom: item.default_unit?.unit_name || 'Unit',
        current_stock: item.inventory ? item.inventory.reduce((sum, inv) => sum + parseInt(inv.quantity), 0) : 0,
        requested_qty: 1,
        remarks: ''
    });

    searchQuery.value = '';
    searchResults.value = [];
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    if (form.items.length === 0) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please add at least one item to request.' });
        return;
    }

    form.post(route('requisitions.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Create Requisition" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('requisitions.index')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </Link>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Create Demand Requisition
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Left Sidebar (Settings) -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">Requisition Details</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Type</label>
                                    <select v-model="form.type" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="Purchase">Purchase (Buy from Supplier)</option>
                                        <option value="Transfer">Transfer (From another Branch)</option>
                                        <option value="Emergency">Emergency Purchase</option>
                                        <option value="Auto">Auto Generated</option>
                                    </select>
                                </div>
                                
                                <div v-if="form.type === 'Purchase'">
                                    <label class="block text-sm font-medium text-slate-700">Supplier</label>
                                    <select v-model="form.supplier_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="">Select Supplier...</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}{{ supplier.companies && supplier.companies.length ? ` (${supplier.companies.join(', ')})` : (supplier.company_name ? ` (${supplier.company_name})` : '') }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Priority</label>
                                    <select v-model="form.priority" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="Low">Low</option>
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Required Date</label>
                                    <input v-model="form.required_date" type="date" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Internal Notes</label>
                                    <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Reason for request..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-sm border border-indigo-100 p-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-indigo-900 mb-1">Smart Auto-Requisition</h3>
                                    <p class="text-xs text-indigo-700 mb-4">Automatically scan your inventory and suggest items based on reorder levels and safety stock.</p>
                                    <button type="button" @click="generateAutoRequisition" :disabled="isGenerating" class="w-full justify-center inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                        {{ isGenerating ? 'Scanning Inventory...' : 'Generate Auto List' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Main Content (Items) -->
                    <div class="md:col-span-2 space-y-6">
                        
                        <!-- Search Box -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input v-model="searchQuery" @input="searchMedicines" type="text" class="block w-full rounded-xl border-slate-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-3" placeholder="Search medicines by name or barcode to add manually...">
                                <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-emerald-500"></div>
                                </div>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div v-if="searchResults.length > 0 && searchQuery" class="absolute z-10 mt-2 w-full bg-white shadow-xl rounded-xl border border-slate-100 py-1 max-h-80 overflow-auto left-0">
                                <div v-for="item in searchResults" :key="item.id" @click="addItem(item)" class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0 flex justify-between items-center transition-colors">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ item.name }}</div>
                                        <div class="text-xs text-slate-500" v-if="item.generic_name">{{ item.generic_name }}</div>
                                    </div>
                                    <button class="text-xs font-semibold bg-white border border-slate-200 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">+ Add</button>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Items List -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-slate-800">Requested Items ({{ form.items.length }})</h3>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Item</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-24">Stock</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">Req Qty</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Remarks</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="(item, index) in form.items" :key="index" class="bg-white">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-slate-900">{{ item.name }}</div>
                                                <div class="text-xs text-slate-500">Unit: {{ item.uom }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-slate-600">{{ item.current_stock }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" v-model="item.requested_qty" min="1" class="block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text" v-model="item.remarks" class="block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2 text-slate-500" placeholder="Optional">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <button type="button" @click="removeItem(index)" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="form.items.length === 0">
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                                                <p class="text-sm font-medium">No items added to the request yet.</p>
                                                <p class="text-xs mt-1">Search manually or use Smart Auto-Requisition.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-4">
                            <Link :href="route('requisitions.index')" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Saving...' : 'Submit Requisition' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>
