<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
    reference_no: String,
});

const form = useForm({
    supplier_id: '',
    reference_no: props.reference_no,
    purchase_date: new Date().toISOString().slice(0, 10),
    discount: 0,
    tax: 0,
    paid_amount: 0,
    notes: '',
    items: [],
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
    let buyPrice = medicine.buy_price || 0;
    let mrp = parseFloat((buyPrice * 1.12).toFixed(2));
    
    form.items.push({ 
        medicine_id: medicine.id, 
        name: medicine.name,
        batch_no: '', 
        expiry_date: '', 
        quantity: 1, 
        unit_price: buyPrice,
        profit_percent: 12,
        mrp: mrp 
    });
    searchQuery.value = '';
    searchResults.value = [];
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
});

const grandTotal = computed(() => {
    return subtotal.value - form.discount + form.tax;
});

const dueAmount = computed(() => {
    return Math.max(0, grandTotal.value - form.paid_amount);
});

const submit = () => {
    form.post(route('purchases.store'));
};
</script>

<template>
    <Head title="New Purchase" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-20">
        <TopNavbar />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-visible">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Order Items
                            </h2>
                        </div>
                        
                        <div class="relative max-w-xl z-20">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                :disabled="!form.supplier_id"
                                :placeholder="form.supplier_id ? 'Search Medicine Name or Barcode...' : 'Please select a Supplier first...'" 
                                class="block w-full pl-10 pr-4 py-2 border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 shadow-sm sm:text-sm disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed transition-colors"
                            >
                            <p v-if="!form.supplier_id" class="mt-1.5 text-xs text-amber-600 font-medium">
                                <svg class="inline w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Select a supplier to load their available medicines.
                            </p>
                            
                            <!-- Dropdown -->
                            <div v-if="searchQuery && searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-200 max-h-60 overflow-y-auto z-50">
                                <ul class="divide-y divide-slate-100">
                                    <li v-for="med in searchResults" :key="med.id" 
                                        @click="addMedicineToCart(med)"
                                        class="p-3 hover:bg-emerald-50 cursor-pointer flex justify-between items-center">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ med.name }}</div>
                                            <div class="text-xs text-slate-500">{{ med.generic_name }}</div>
                                        </div>
                                        <div class="text-emerald-600 font-semibold text-sm">
                                            + Add
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto relative z-10">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="py-3 pl-4 text-left text-xs font-semibold text-slate-500 w-[20%]">Medicine</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[15%]">Batch</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[12%]">Expiry</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[8%]">Qty</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[12%]">Buy Price ($)</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[10%]">Profit %</th>
                                    <th scope="col" class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[12%]">Sell Price (MRP)</th>
                                    <th scope="col" class="py-3 px-3 text-right text-xs font-semibold text-slate-500">Subtotal</th>
                                    <th scope="col" class="py-3 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-if="form.items.length === 0">
                                    <td colspan="9" class="py-8 text-center text-slate-500 text-sm">
                                        No items added. Search for a medicine above to add to your order.
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in form.items" :key="index" class="bg-white">
                                    <td class="py-2 pl-4">
                                        <div class="font-semibold text-slate-900 text-sm min-w-[150px]">{{ item.name }}</div>
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.batch_no" type="text" placeholder="Optional" class="block w-full min-w-[100px] rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.expiry_date" type="date" class="block w-full min-w-[130px] rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.quantity" type="number" min="1" required class="block w-full min-w-[80px] rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.unit_price" @input="item.mrp = parseFloat((item.unit_price * (1 + item.profit_percent / 100)).toFixed(2))" type="number" step="0.01" min="0" required class="block w-full min-w-[100px] rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.profit_percent" @input="item.mrp = parseFloat((item.unit_price * (1 + item.profit_percent / 100)).toFixed(2))" type="number" step="0.1" required class="block w-full min-w-[80px] rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input v-model="item.mrp" @input="item.unit_price > 0 ? item.profit_percent = parseFloat((((item.mrp - item.unit_price) / item.unit_price) * 100).toFixed(1)) : 0" type="number" step="0.01" min="0" required class="block w-full min-w-[100px] rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                    </td>
                                    <td class="py-2 px-3 text-right font-medium text-slate-700">
                                        ${{ (item.quantity * item.unit_price).toFixed(2) }}
                                    </td>
                                    <td class="py-2 pr-4 text-right">
                                        <button @click.prevent="removeItem(index)" class="text-slate-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bottom Section: Totals & Payment -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Purchase Notes</label>
                        <textarea v-model="form.notes" rows="4" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Any additional notes or remarks..."></textarea>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Subtotal:</span>
                            <span class="font-medium text-slate-900">${{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Discount (-):</span>
                            <input v-model="form.discount" type="number" step="0.01" min="0" class="w-32 rounded-lg border-slate-300 py-1 px-2 text-right text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Tax (+):</span>
                            <input v-model="form.tax" type="number" step="0.01" min="0" class="w-32 rounded-lg border-slate-300 py-1 px-2 text-right text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <hr class="border-slate-100">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-slate-900">Grand Total:</span>
                            <span class="text-lg font-bold text-emerald-600">${{ grandTotal.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-4">
                            <span class="text-slate-700 font-medium">Paid Amount:</span>
                            <input v-model="form.paid_amount" type="number" step="0.01" min="0" class="w-32 rounded-lg border-slate-300 py-1.5 px-2 text-right text-sm focus:border-emerald-500 focus:ring-emerald-500 font-semibold bg-emerald-50 text-emerald-900">
                        </div>
                        <div class="flex justify-between items-center text-sm bg-rose-50 p-3 rounded-lg border border-rose-100">
                            <span class="text-rose-700 font-semibold">Due to Supplier:</span>
                            <span class="text-rose-700 font-bold">${{ dueAmount.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="submit" :disabled="form.processing || form.items.length === 0" class="inline-flex justify-center rounded-xl bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Confirm Purchase & Add Stock' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
