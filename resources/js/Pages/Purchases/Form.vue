<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
    reference_no: String,
    prefillPo: Object,
});

const initialItems = props.prefillPo ? props.prefillPo.items.map(poItem => {
    return {
        medicine_id: poItem.item.id,
        name: poItem.item.name,
        available_units: poItem.item.units || [],
        unit_id: poItem.unit_id,
        unit_factor: 1,
        unit_name: 'Unit',
        batch_no: '',
        expiry_date: '',
        quantity: poItem.ordered_qty,
        bonus_quantity: 0,
        unit_price: poItem.unit_price,
        mrp: poItem.item.sell_price || poItem.unit_price * 1.12,
        trade_discount_percent: 0,
        purchase_order_item_id: poItem.id
    };
}) : [];

const form = useForm({
    supplier_id: props.prefillPo ? props.prefillPo.supplier_id : '',
    purchase_order_id: props.prefillPo ? props.prefillPo.id : null,
    reference_no: props.reference_no,
    purchase_date: new Date().toISOString().slice(0, 10),
    discount: 0,
    tax: 0,
    paid_amount: 0,
    notes: props.prefillPo ? `GRN for PO #${props.prefillPo.po_no}` : '',
    items: initialItems,
});

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);

watch(() => form.supplier_id, () => {
    searchQuery.value = '';
    searchResults.value = [];
});

watch(searchQuery, async (newVal) => {
    if (!newVal || !form.supplier_id) {
        searchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await fetch(route('purchases.search', { q: newVal, supplier_id: form.supplier_id }));
        const data = await response.json();
        searchResults.value = data;
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isSearching.value = false;
    }
});

const addMedicineToCart = (medicine) => {
    let mrp = parseFloat(medicine.mrp) || parseFloat(medicine.sell_price) || 0;
    
    // In BD standard trade discount is usually 12% off MRP
    let buyPrice = parseFloat((mrp * 0.88).toFixed(2));
    
    let defaultUnit = medicine.default_unit || null;
    if (defaultUnit && defaultUnit.factor > 0) {
        buyPrice = buyPrice * defaultUnit.factor;
        mrp = mrp * defaultUnit.factor;
    }

    form.items.push({ 
        medicine_id: medicine.id, 
        name: medicine.name,
        available_units: medicine.units || [],
        unit_id: defaultUnit ? defaultUnit.id : null,
        unit_factor: defaultUnit ? defaultUnit.factor : 1,
        unit_name: defaultUnit ? defaultUnit.unit_name : 'Unit',
        batch_no: '', 
        expiry_date: '', 
        quantity: 1, 
        bonus_quantity: 0,
        unit_price: buyPrice, // TP
        mrp: mrp,
        trade_discount_percent: 12
    });
    searchQuery.value = '';
    searchResults.value = [];
};

const onUnitChange = (item) => {
    if (!item.unit_id) return;
    let selectedUnit = item.available_units.find(u => u.id === item.unit_id);
    if (selectedUnit) {
        item.unit_factor = selectedUnit.factor;
        item.unit_name = selectedUnit.unit_name;
    }
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calcActualCost = (item) => {
    let q = parseFloat(item.quantity) || 0;
    let b = parseFloat(item.bonus_quantity) || 0;
    let totalQty = q + b;
    if (totalQty === 0) return 0;
    return (q * parseFloat(item.unit_price || 0)) / totalQty;
};

const calcActualMargin = (item) => {
    let cost = calcActualCost(item);
    if (cost === 0) return 0;
    let mrp = parseFloat(item.mrp) || 0;
    return (((mrp - cost) / cost) * 100).toFixed(1);
};

const updateTP = (item) => {
    let mrp = parseFloat(item.mrp) || 0;
    let discount = parseFloat(item.trade_discount_percent) || 0;
    item.unit_price = parseFloat((mrp - (mrp * discount / 100)).toFixed(2));
};

const updateDiscount = (item) => {
    let mrp = parseFloat(item.mrp) || 0;
    let tp = parseFloat(item.unit_price) || 0;
    if (mrp > 0) {
        item.trade_discount_percent = parseFloat((((mrp - tp) / mrp) * 100).toFixed(2));
    } else {
        item.trade_discount_percent = 0;
    }
};

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
});

const grandTotal = computed(() => {
    return subtotal.value - form.discount + form.tax;
});

const dueAmount = computed(() => {
    return Math.max(0, grandTotal.value - form.paid_amount);
});

import Swal from 'sweetalert2';

const submit = () => {
    form.transform((data) => ({
        ...data,
        items: data.items.map(item => ({
            ...item,
            unit_id: item.unit_id === 'fallback' ? null : item.unit_id
        }))
    })).post(route('purchases.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Purchase Saved',
                text: 'The purchase order has been successfully saved to inventory.',
                confirmButtonColor: '#10b981',
                timer: 2000,
                showConfirmButton: false
            });
        },
        onError: (errors) => {
            // Check if there are specific item errors or general errors
            const errorMessages = Object.values(errors).join('\n');
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessages || 'Please check the form for errors.',
                confirmButtonColor: '#ef4444',
            });
        }
    });
};
</script>

<template>
    <Head title="New Purchase" />

    <AppLayout>

        <main class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Top Section: General Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Purchase Details
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Supplier</label>
                            <select v-model="form.supplier_id" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option value="" disabled>Select Supplier...</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }} ({{ supplier.companies && supplier.companies.length ? supplier.companies.join(', ') : 'Individual' }})
                                </option>
                            </select>
                            <p v-if="form.errors.supplier_id" class="mt-1 text-sm text-red-500">{{ form.errors.supplier_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Reference / Invoice No</label>
                            <input v-model="form.reference_no" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <p v-if="form.errors.reference_no" class="mt-1 text-sm text-red-500">{{ form.errors.reference_no }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Purchase Date</label>
                            <input v-model="form.purchase_date" type="date" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Middle Section: Items -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-visible p-6">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Order Items</h2>
                        </div>
                        <div class="flex items-center gap-5">
                            <label class="flex items-center cursor-pointer gap-2.5">
                                <span class="text-[13px] text-slate-600 font-semibold">Show Cost Price</span>
                                <div class="relative">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-10 h-6 bg-blue-500 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all shadow-inner"></div>
                                </div>
                            </label>
                            <button type="button" class="flex items-center gap-1.5 px-4 py-2 bg-blue-50/70 border border-blue-100 text-blue-600 rounded-xl text-[13px] font-bold hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Help
                            </button>
                        </div>
                    </div>
                        
                    <!-- Search Bar Area -->
                    <div class="flex gap-3 mb-8">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                id="search-medicine-input"
                                v-model="searchQuery" 
                                type="text" 
                                :disabled="!form.supplier_id"
                                :placeholder="form.supplier_id ? 'Search Medicine by Name or Barcode' : 'Please select a Supplier first...'" 
                                class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-colors disabled:bg-slate-50 disabled:text-slate-400 font-medium"
                            />
                            
                            <!-- Dropdown -->
                            <div v-if="searchQuery && searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-200 max-h-60 overflow-y-auto z-50">
                                <ul class="divide-y divide-slate-100">
                                    <li v-for="med in searchResults" :key="med.id" 
                                        @click="addMedicineToCart(med)"
                                        class="p-3 hover:bg-blue-50 cursor-pointer flex justify-between items-center transition-colors">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ med.name }}</div>
                                            <div class="text-xs text-slate-500">{{ med.generic_name }}</div>
                                        </div>
                                        <div class="text-blue-600 font-bold text-sm bg-white border border-blue-200 px-3 py-1 rounded-lg shadow-sm">
                                            + Add
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 bg-white text-slate-700 rounded-xl hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add New Medicine
                        </button>
                        <button type="button" class="flex items-center gap-2 px-4 py-2.5 bg-blue-50/70 border border-blue-100 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors text-[13px] font-bold shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Scan Barcode
                        </button>
                    </div>
                    
                    <!-- Table Header -->
                    <div class="flex items-center gap-2 px-2 pb-3 pt-2 text-[10px] font-black text-slate-500 border-b border-slate-100 uppercase tracking-widest leading-tight">
                        <div class="w-[18%]">Medicine <span class="text-red-500">*</span></div>
                        <div class="w-[8%] flex items-center justify-between">Batch No. <span class="text-slate-300 font-normal">ⓘ</span></div>
                        <div class="w-[10%] flex items-center justify-between">Expiry Date <span class="text-red-500">*</span> <span class="text-slate-300 font-normal">ⓘ</span></div>
                        <div class="w-[8%] text-center flex items-center justify-center gap-1">Unit <span class="text-slate-300 font-normal">ⓘ</span></div>
                        <div class="w-[6%] text-center flex flex-col items-center"><div>Qty <span class="text-red-500">*</span></div><div class="text-[8px] font-medium text-slate-400 capitalize">(Pur. Unit)</div></div>
                        <div class="w-[6%] text-center flex items-center justify-center gap-1">Bonus <span class="text-slate-300 font-normal">ⓘ</span></div>
                        <div class="w-[7%] text-center flex flex-col items-center"><div>MRP <span class="text-red-500">*</span></div><div class="text-[8px] font-medium text-slate-400 capitalize">(Per Unit)</div></div>
                        <div class="w-[6%] text-center">Disc %</div>
                        <div class="w-[8%] text-center flex flex-col items-center"><div>TP / Buy <span class="text-red-500">*</span></div><div class="text-[8px] font-medium text-slate-400 capitalize">(Per Unit)</div></div>
                        <div class="w-[7%] text-center flex flex-col items-center"><div>Act. Cost</div><div class="text-[8px] font-medium text-slate-400 capitalize">(Per Unit)</div></div>
                        <div class="w-[6%] text-center">Margin %</div>
                        <div class="w-[6%] text-center flex flex-col items-center"><div>Subtotal</div><div class="text-[8px] font-medium text-slate-400 capitalize">(TP)</div></div>
                        <div class="w-[4%] text-right">Action</div>
                    </div>

                    <div v-if="form.items.length === 0" class="py-16 text-center text-slate-400 text-sm font-medium border-b border-slate-100 mb-6">
                        No items added. Search for a medicine above to begin.
                    </div>

                    <!-- Item Cards -->
                    <div class="mt-4">
                        <div v-for="(item, index) in form.items" :key="index" class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
                            <!-- Top Row Inputs -->
                            <div class="flex items-center gap-2 p-3 border-b border-slate-100 bg-white">
                                <!-- Medicine -->
                                <div class="w-[18%]">
                                    <div class="border border-slate-200 rounded-xl py-1.5 px-2.5 bg-slate-50 relative flex flex-col justify-center min-h-[42px]">
                                        <div class="text-[12px] font-black text-slate-900 truncate">{{ item.name }}</div>
                                        <div class="text-[9px] font-medium text-slate-500 uppercase tracking-widest">(ID: {{ item.medicine_id }})</div>
                                    </div>
                                </div>
                                <!-- Batch No -->
                                <div class="w-[8%]">
                                    <input v-model="item.batch_no" type="text" placeholder="e.g. B12" class="w-full border-slate-200 rounded-xl text-[12px] font-semibold focus:ring-blue-500 py-2.5 px-2 shadow-sm min-h-[42px]">
                                </div>
                                <!-- Expiry -->
                                <div class="w-[10%]">
                                    <input v-model="item.expiry_date" type="date" class="w-full border-slate-200 rounded-xl text-[12px] font-semibold focus:ring-blue-500 py-2.5 px-2 shadow-sm text-slate-700 min-h-[42px]">
                                </div>
                                <!-- Unit -->
                                <div class="w-[8%]">
                                    <select v-if="item.available_units && item.available_units.length > 0" v-model="item.unit_id" @change="onUnitChange(item)" class="w-full border-slate-200 rounded-xl text-[12px] font-bold focus:ring-blue-500 py-2.5 px-2 bg-slate-50 shadow-sm text-slate-800 min-h-[42px]">
                                        <option v-for="u in item.available_units" :key="u.id" :value="u.id">{{ u.unit_name }}</option>
                                    </select>
                                    <select v-else disabled class="w-full border-slate-200 rounded-xl text-[12px] font-bold py-2.5 px-2 bg-slate-50 shadow-sm text-slate-400 min-h-[42px]">
                                        <option>Unit</option>
                                    </select>
                                </div>
                                <!-- Qty -->
                                <div class="w-[6%]">
                                    <input v-model="item.quantity" type="number" min="1" class="w-full border-slate-200 rounded-xl text-[13px] font-black focus:ring-blue-500 py-2.5 px-1 text-center shadow-sm text-slate-900 min-h-[42px]">
                                </div>
                                <!-- Bonus Qty -->
                                <div class="w-[6%]">
                                    <input v-model="item.bonus_quantity" type="number" min="0" class="w-full border-slate-200 rounded-xl text-[13px] font-bold focus:ring-blue-500 py-2.5 px-1 text-center bg-slate-50 shadow-sm text-slate-600 min-h-[42px]">
                                </div>
                                <!-- MRP -->
                                <div class="w-[7%]">
                                    <input v-model="item.mrp" @input="updateDiscount(item)" type="number" step="0.01" min="0" class="w-full border-slate-200 rounded-xl text-[13px] font-bold focus:ring-blue-500 py-2.5 px-2 text-right bg-slate-50 shadow-sm min-h-[42px]">
                                </div>
                                <!-- Disc % -->
                                <div class="w-[6%] relative">
                                    <input v-model="item.trade_discount_percent" @input="updateTP(item)" type="number" step="0.01" class="w-full border-slate-200 rounded-xl text-[13px] font-bold focus:ring-blue-500 py-2.5 pl-1 text-center shadow-sm text-slate-700 pr-4 min-h-[42px]">
                                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] font-black pointer-events-none">%</span>
                                </div>
                                <!-- TP -->
                                <div class="w-[8%]">
                                    <input v-model="item.unit_price" @input="updateDiscount(item)" type="number" step="0.01" min="0" class="w-full border-blue-200 rounded-xl text-[13px] focus:ring-blue-500 py-2.5 px-2 text-right bg-blue-50 shadow-sm font-black text-blue-800 min-h-[42px]">
                                </div>
                                <!-- Act Cost -->
                                <div class="w-[7%] text-center flex flex-col justify-center min-h-[42px]">
                                    <div class="text-[13px] font-black text-slate-900 leading-tight">{{ calcActualCost(item).toFixed(2) }}</div>
                                    <div class="text-[9px] font-medium text-slate-400 uppercase">(Per {{ item.unit_name }})</div>
                                </div>
                                <!-- Margin % -->
                                <div class="w-[6%] text-center flex justify-center items-center min-h-[42px]">
                                    <div class="px-2 py-1.5 rounded-lg text-[11px] font-black shadow-sm border" :class="calcActualMargin(item) > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'">
                                        {{ calcActualMargin(item) }}%
                                    </div>
                                </div>
                                <!-- Subtotal -->
                                <div class="w-[6%] text-center flex justify-center items-center min-h-[42px]">
                                    <div class="text-[13px] font-black text-slate-900">{{ ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)).toFixed(2) }}</div>
                                </div>
                                <!-- Action -->
                                <div class="w-[4%] text-right flex justify-end items-center min-h-[42px]">
                                    <button @click.prevent="removeItem(index)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 hover:text-red-600 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Info Panels Below Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-4 bg-slate-50">
                                <!-- Unit Conversion -->
                                <div class="border border-slate-200 bg-white rounded-2xl p-4 shadow-sm flex flex-col">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <h4 class="text-[13px] font-black text-blue-600 tracking-tight">Unit Conversion</h4>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center">
                                        <div class="text-[11px] text-slate-600 font-semibold space-y-1" v-if="item.available_units && item.available_units.length > 0">
                                            <div v-for="u in item.available_units" :key="u.id" class="flex justify-between items-center border-b border-slate-50 pb-1">
                                                <span>1 {{ u.unit_name }}</span>
                                                <span class="text-slate-400">=</span>
                                                <span>{{ u.factor }} Base Unit</span>
                                            </div>
                                        </div>
                                        <div v-else class="text-[11px] text-slate-400 italic">No hierarchy defined.</div>
                                    </div>
                                    <div class="bg-blue-50/70 border border-blue-100 text-blue-700 px-3 py-2 rounded-xl mt-3 text-[11px] font-bold flex items-center justify-between">
                                        <span>Current: 1 {{ item.unit_name }} = {{ item.unit_factor }} Base</span>
                                    </div>
                                </div>
                                
                                <!-- Offer / Scheme -->
                                <div class="border border-slate-200 bg-white rounded-2xl p-4 shadow-sm flex gap-5">
                                    <div class="flex-1 border-r border-slate-100 pr-4 flex flex-col justify-center">
                                        <div class="flex items-center gap-2 mb-3">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                                            <h4 class="text-[13px] font-black text-blue-600 tracking-tight">Offer / Scheme</h4>
                                        </div>
                                        <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Offer Type</label>
                                        <select class="w-full border-slate-200 bg-slate-50 rounded-xl text-xs font-bold py-2 px-3 focus:ring-blue-500 shadow-sm text-slate-700">
                                            <option>Manual Entry</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center pt-2">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">You Will Get</div>
                                        <div class="text-[13px] font-black text-emerald-600 mb-3">{{ item.quantity }} + {{ item.bonus_quantity }} ({{ item.bonus_quantity }} {{ item.unit_name }} Free)</div>
                                        
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Stock Added</div>
                                        <div class="text-[13px] font-black text-slate-900">{{ parseFloat(item.quantity || 0) + parseFloat(item.bonus_quantity || 0) }} {{ item.unit_name }} ({{ (parseFloat(item.quantity || 0) + parseFloat(item.bonus_quantity || 0)) * item.unit_factor }} Base Unit)</div>
                                    </div>
                                </div>

                                <!-- Quick Calculation -->
                                <div class="border border-slate-200 bg-white rounded-2xl p-4 shadow-sm flex flex-col">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-5 h-5 bg-pink-100 rounded-md flex items-center justify-center">
                                            <svg class="w-3 h-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <h4 class="text-[13px] font-black text-blue-600 tracking-tight">Quick Calculation</h4>
                                    </div>
                                    <div class="space-y-2.5 text-[12px] font-bold text-slate-600 flex-1 flex flex-col justify-center">
                                        <div class="flex justify-between items-center">
                                            <span>MRP</span>
                                            <span class="text-slate-900">{{ item.mrp ? parseFloat(item.mrp).toFixed(2) : '0.00' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span>Discount ({{ item.trade_discount_percent ? item.trade_discount_percent : 0 }}%)</span>
                                            <span class="text-slate-900">{{ ((parseFloat(item.mrp) || 0) * (parseFloat(item.trade_discount_percent) || 0) / 100).toFixed(2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                                            <span class="text-slate-900">TP / Buy Price</span>
                                            <span class="text-blue-600 font-black text-sm">{{ item.unit_price ? parseFloat(item.unit_price).toFixed(2) : '0.00' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button @click="document.getElementById('search-medicine-input').focus()" type="button" class="w-full flex items-center justify-center gap-2 py-4 border-2 border-blue-100 border-dashed rounded-2xl text-blue-600 font-bold hover:bg-blue-50 transition-colors shadow-sm bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Another Item
                        </button>
                    </div>
                </div>

                <!-- Bottom Section: Totals & Payment -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- Purchase Notes -->
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex flex-col">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center border border-purple-100">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-[14px] font-black text-slate-900 tracking-tight">Purchase Notes</h3>
                        </div>
                        <textarea v-model="form.notes" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-[13px] font-medium focus:ring-blue-500 focus:border-blue-500 flex-1 shadow-inner text-slate-700" placeholder="Type any additional notes..."></textarea>
                    </div>

                    <!-- Summary -->
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <h3 class="text-[14px] font-black text-slate-900 tracking-tight">Summary</h3>
                        </div>
                        <div class="space-y-3.5 text-[13px] font-bold text-slate-600">
                            <div class="flex justify-between items-center">
                                <span>Total Items</span>
                                <span class="text-slate-900">{{ form.items.length }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Total Qty <span class="text-[10px] text-slate-400 font-semibold">(Purchase Unit)</span></span>
                                <span class="text-slate-900">{{ form.items.reduce((s, i) => s + parseFloat(i.quantity || 0), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Total Bonus Qty</span>
                                <span class="text-slate-900">{{ form.items.reduce((s, i) => s + parseFloat(i.bonus_quantity || 0), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Total Stock Added <span class="text-[10px] text-slate-400 font-semibold">(Purchase Unit)</span></span>
                                <span class="text-slate-900">{{ form.items.reduce((s, i) => s + parseFloat(i.quantity || 0) + parseFloat(i.bonus_quantity || 0), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                                <span class="text-slate-900 font-black text-[14px]">Grand Total (TP)</span>
                                <span class="text-blue-600 font-black text-[15px]">${{ subtotal.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-5">
                                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center border border-green-100">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <h3 class="text-[14px] font-black text-slate-900 tracking-tight">Payment Summary</h3>
                            </div>
                            <div class="space-y-4 text-[13px] font-bold text-slate-600">
                                <div class="flex justify-between items-center">
                                    <span>Total Amount (TP)</span>
                                    <span class="text-slate-900">${{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Discount / Other (-)</span>
                                    <input v-model="form.discount" type="number" step="0.01" min="0" class="w-24 text-right border-slate-200 rounded-xl py-1.5 px-3 text-[12px] font-black focus:ring-blue-500 bg-slate-50 shadow-inner">
                                </div>
                                <div class="flex justify-between items-center hidden">
                                    <span>Tax (+)</span>
                                    <input v-model="form.tax" type="number" step="0.01" min="0" class="w-24 text-right border-slate-200 rounded-xl py-1.5 px-3 text-[12px] font-black focus:ring-blue-500 bg-slate-50 shadow-inner">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-[15px] font-black text-slate-900">Net Payable</span>
                                <span class="text-2xl font-black text-green-600">${{ grandTotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-600 font-bold">Paid Amount</span>
                                <input v-model="form.paid_amount" type="number" step="0.01" min="0" class="w-32 text-right border border-green-200 rounded-xl py-2 px-3 text-[14px] focus:ring-green-500 bg-green-50 text-green-800 font-black shadow-inner">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-slate-200 mt-2 mb-2">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" @click="form.items = []" class="flex-1 sm:flex-none px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-[13px] font-bold hover:bg-slate-50 transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Clear All
                        </button>
                        <button type="button" class="flex-1 sm:flex-none px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-[13px] font-bold hover:bg-slate-50 transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Save as Draft
                        </button>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <Link :href="route('purchases.index')" class="flex-1 sm:flex-none px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[13px] font-bold hover:bg-slate-50 transition-colors text-center shadow-sm">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing || form.items.length === 0" class="flex-1 sm:flex-none px-10 py-3 bg-blue-600 text-white rounded-2xl text-[14px] font-black hover:bg-blue-700 transition-colors shadow-md disabled:opacity-50 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            {{ form.processing ? 'Saving...' : 'Save Purchase' }}
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </AppLayout>
</template>
