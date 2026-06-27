<script setup>
import { ref, watch } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    suppliers: Array,
    requisitions: Array,
});

const form = useForm({
    requisition_id: '',
    supplier_id: '',
    expected_delivery_date: '',
    items: []
});

watch(() => form.requisition_id, (newReqId) => {
    if (newReqId) {
        const req = props.requisitions.find(r => r.id === newReqId);
        if (req && req.items) {
            // Populate form items
            form.items = req.items.map(ri => {
                const item = ri.item;
                return {
                    item_id: item.id,
                    name: item.name,
                    uom: item.uom?.name || 'Unit',
                    ordered_qty: ri.approved_qty > 0 ? ri.approved_qty : ri.requested_qty,
                    unit_price: item.buy_price || 0
                };
            });
            // Try to set expected delivery date if available
            if (req.required_date) {
                form.expected_delivery_date = req.required_date;
            }

            // Auto-select supplier based on items
            if (req.items.length > 0) {
                let targetSupplierId = req.items.find(ri => ri.item.preferred_supplier_id)?.item.preferred_supplier_id;
                
                if (!targetSupplierId && props.suppliers && props.suppliers.length > 0) {
                    for (const ri of req.items) {
                        const mfgName = ri.item.manufacturer?.name;
                        if (mfgName) {
                            const cleanMfg = mfgName.replace(/ Ltd\.| Ltd| Limited| PLC| Plc| PLC\.|\./gi, '').trim().toLowerCase();
                            const matchedSupplier = props.suppliers.find(sup => {
                                if (sup.companies && Array.isArray(sup.companies)) {
                                    return sup.companies.some(c => c.replace(/ Ltd\.| Ltd| Limited| PLC| Plc| PLC\.|\./gi, '').trim().toLowerCase().includes(cleanMfg) || cleanMfg.includes(c.replace(/ Ltd\.| Ltd| Limited| PLC| Plc| PLC\.|\./gi, '').trim().toLowerCase()));
                                }
                                return false;
                            });
                            if (matchedSupplier) {
                                targetSupplierId = matchedSupplier.id;
                                break;
                            }
                        }
                    }
                }

                if (targetSupplierId) {
                    form.supplier_id = targetSupplierId;
                }
            }
        }
    }
});

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;

const searchMedicines = () => {
    clearTimeout(searchTimeout);
    
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        isSearching.value = true;
        try {
            const response = await axios.get(route('requisitions.search'), {
                params: { 
                    q: searchQuery.value,
                    supplier_id: form.supplier_id 
                }
            });
            searchResults.value = response.data;
        } catch (error) {
            console.error('Error searching medicines:', error);
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const addItem = (item) => {
    // Check if item already exists
    const exists = form.items.find(i => i.item_id === item.id);
    if (!exists) {
        form.items.push({
            item_id: item.id,
            name: item.name,
            uom: item.uom?.name || 'Unit',
            ordered_qty: 1,
            unit_price: item.buy_price || 0
        });
    }
    
    // Clear search
    searchQuery.value = '';
    searchResults.value = [];
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('purchase-orders.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Create Purchase Order" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('purchase-orders.index')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Create Purchase Order
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                PO Details
                            </h3>
                            
                            <div class="space-y-4">
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Source Requisition (Optional)</label>
                                    <select v-model="form.requisition_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="">Manual Entry (No Requisition)</option>
                                        <option v-for="req in requisitions" :key="req.id" :value="req.id">
                                            {{ req.requisition_no }} - {{ req.type }} ({{ new Date(req.created_at).toLocaleDateString() }})
                                        </option>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Select a requisition to auto-fill items.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Supplier</label>
                                    <select v-model="form.supplier_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                        <option value="">Select Supplier...</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}{{ supplier.companies && supplier.companies.length ? ` (${supplier.companies.join(', ')})` : (supplier.company_name ? ` (${supplier.company_name})` : '') }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Select supplier to filter items.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Expected Delivery Date</label>
                                    <input type="date" v-model="form.expected_delivery_date" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Items -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Search Box -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input v-model="searchQuery" @input="searchMedicines" type="text" class="block w-full rounded-xl border-slate-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-3" placeholder="Search items by name or barcode to add to PO...">
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
                                    <button type="button" class="text-xs font-semibold bg-white border border-slate-200 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">+ Add</button>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Items List -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-slate-800">PO Items ({{ form.items.length }})</h3>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Item</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">Qty</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">Unit Price ($)</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-32">Total ($)</th>
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
                                                <input type="number" v-model="item.ordered_qty" min="1" class="block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" step="0.01" v-model="item.unit_price" min="0" class="block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-1.5 px-2">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-slate-900">{{ (item.ordered_qty * item.unit_price).toFixed(2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <button type="button" @click="removeItem(index)" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="form.items.length === 0">
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                                                <p class="text-sm font-medium">No items added to the PO yet.</p>
                                                <p class="text-xs mt-1">Search manually to add items.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot v-if="form.items.length > 0" class="bg-slate-50 border-t border-slate-200">
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-700">Total Amount:</td>
                                            <td colspan="2" class="px-6 py-4 text-left text-sm font-bold text-emerald-600">
                                                ${{ form.items.reduce((acc, item) => acc + (item.ordered_qty * item.unit_price), 0).toFixed(2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-4">
                            <Link :href="route('purchase-orders.index')" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing || form.items.length === 0 || !form.supplier_id" class="inline-flex justify-center rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Saving...' : 'Create Purchase Order' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>
