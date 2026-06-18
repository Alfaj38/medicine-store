<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
    reasons: Array,
});

const form = useForm({
    supplier_id: '',
    return_date: new Date().toISOString().slice(0, 10),
    notes: '',
    items: [],
});

const selectedMedicineId = ref('');
const availableMedicines = ref([]);
const availableBatches = ref([]);
const isLoadingMedicines = ref(false);
const isLoadingBatches = ref(false);

watch(() => form.supplier_id, async (supplierId) => {
    selectedMedicineId.value = '';
    availableMedicines.value = [];
    availableBatches.value = [];
    if (!supplierId) return;

    isLoadingMedicines.value = true;
    try {
        const response = await fetch(route('purchase-returns.medicines', { supplier_id: supplierId }));
        availableMedicines.value = await response.json();
    } catch (error) {
        console.error('Failed to load medicines:', error);
    } finally {
        isLoadingMedicines.value = false;
    }
});

watch(selectedMedicineId, async (medicineId) => {
    availableBatches.value = [];
    if (!form.supplier_id || !medicineId) return;

    isLoadingBatches.value = true;
    try {
        const response = await fetch(route('purchase-returns.batches', { 
            supplier_id: form.supplier_id, 
            medicine_id: medicineId 
        }));
        availableBatches.value = await response.json();
    } catch (error) {
        console.error('Failed to load batches:', error);
    } finally {
        isLoadingBatches.value = false;
    }
});

const addBatchToReturn = (batch) => {
    // Check if already added
    if (form.items.some(item => item.medicine_id === selectedMedicineId.value && item.batch_no === batch.batch_no)) {
        alert('This batch is already added to the return list.');
        return;
    }

    const medicine = availableMedicines.value.find(m => m.id === selectedMedicineId.value);
    
    form.items.push({
        medicine_id: medicine.id,
        medicine_name: medicine.name,
        batch_no: batch.batch_no,
        expiry_date: batch.expiry_date,
        available_qty: batch.available_qty,
        return_qty: 1,
        unit_price: batch.unit_price,
        return_reason_id: props.reasons[0]?.id || ''
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const grandTotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.return_qty * item.unit_price), 0);
});

const submit = () => {
    form.post(route('purchase-returns.store'));
};
</script>

<template>
    <Head title="New Purchase Return - MediSaaS" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-20">
        <nav class="bg-white border-b border-slate-200 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <Link :href="route('purchase-returns.index')" class="flex items-center gap-3 group">
                        <div class="text-slate-400 group-hover:text-emerald-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </div>
                        <h1 class="font-bold text-xl tracking-tight text-slate-800 group-hover:text-emerald-600 transition-colors">New Purchase Return</h1>
                    </Link>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Top Section: General Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                        Return Details
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Supplier</label>
                            <select v-model="form.supplier_id" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option value="" disabled>Select Supplier...</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }} ({{ supplier.companies ? supplier.companies.join(', ') : 'Individual' }})
                                </option>
                            </select>
                            <p v-if="form.errors.supplier_id" class="mt-1 text-sm text-red-500">{{ form.errors.supplier_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Return Date</label>
                            <input v-model="form.return_date" type="date" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <p v-if="form.errors.return_date" class="mt-1 text-sm text-red-500">{{ form.errors.return_date }}</p>
                        </div>
                    </div>
                </div>

                <!-- Middle Section: Batch Selection (FEFO) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Select Medicines to Return
                    </h2>

                    <div class="max-w-xl mb-6">
                        <label class="block text-sm font-medium text-slate-700">Medicine</label>
                        <select v-model="selectedMedicineId" :disabled="!form.supplier_id || isLoadingMedicines" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm disabled:bg-slate-50 disabled:text-slate-500">
                            <option value="" disabled>
                                {{ isLoadingMedicines ? 'Loading medicines...' : (form.supplier_id ? (availableMedicines.length ? 'Select Medicine...' : 'No valid medicines found for this supplier') : 'Select a Supplier first') }}
                            </option>
                            <option v-for="medicine in availableMedicines" :key="medicine.id" :value="medicine.id">
                                {{ medicine.name }} {{ medicine.generic_name ? `(${medicine.generic_name})` : '' }}
                            </option>
                        </select>
                    </div>

                    <!-- FEFO Batch Suggestions -->
                    <div v-if="isLoadingBatches" class="text-sm text-slate-500 py-4">Loading available batches...</div>
                    <div v-else-if="selectedMedicineId && availableBatches.length > 0" class="mb-8">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Available Batches (Sorted by Nearest Expiry)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="batch in availableBatches" :key="batch.batch_no" class="border border-slate-200 rounded-xl p-4 bg-slate-50 hover:border-emerald-300 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-slate-900">Batch: {{ batch.batch_no }}</div>
                                    <div class="text-xs font-semibold px-2 py-1 bg-rose-100 text-rose-700 rounded-md">
                                        Exp: {{ batch.expiry_date }}
                                    </div>
                                </div>
                                <div class="text-sm text-slate-600 mb-3">Available Qty: <span class="font-bold text-slate-900">{{ batch.available_qty }}</span></div>
                                <button type="button" @click="addBatchToReturn(batch)" class="w-full py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    + Add to Return List
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="selectedMedicineId && availableBatches.length === 0" class="text-sm text-rose-500 font-medium py-4">
                        No available inventory found for this medicine purchased from this supplier.
                    </div>
                </div>

                <!-- Bottom Section: Return Items -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-visible">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-900">Return Items</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 pl-4 text-left text-xs font-semibold text-slate-500 w-[20%]">Medicine</th>
                                    <th class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[15%]">Batch & Expiry</th>
                                    <th class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[15%]">Return Qty</th>
                                    <th class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[15%]">Unit Price ($)</th>
                                    <th class="py-3 px-3 text-left text-xs font-semibold text-slate-500 w-[15%]">Reason</th>
                                    <th class="py-3 px-3 text-right text-xs font-semibold text-slate-500">Subtotal</th>
                                    <th class="py-3 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-if="form.items.length === 0">
                                    <td colspan="7" class="py-8 text-center text-slate-500 text-sm">
                                        No items added. Select a medicine and add batches from the section above.
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in form.items" :key="index" class="bg-white">
                                    <td class="py-3 pl-4">
                                        <div class="font-semibold text-slate-900 text-sm">{{ item.medicine_name }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-sm font-medium text-slate-700">{{ item.batch_no }}</div>
                                        <div class="text-xs text-rose-500">{{ item.expiry_date }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2">
                                            <input v-model="item.return_qty" type="number" min="1" :max="item.available_qty" required class="block w-20 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                            <span class="text-xs text-slate-500">/ {{ item.available_qty }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input v-model="item.unit_price" type="number" step="0.01" min="0" required class="block w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5 bg-slate-50" readonly>
                                    </td>
                                    <td class="py-3 px-3">
                                        <select v-model="item.return_reason_id" required class="block w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                            <option v-for="r in reasons" :key="r.id" :value="r.id">{{ r.code }} - {{ r.reason }}</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-3 text-right font-medium text-rose-600">
                                        ${{ (item.return_qty * item.unit_price).toFixed(2) }}
                                    </td>
                                    <td class="py-3 pr-4 text-right">
                                        <button @click.prevent="removeItem(index)" class="text-slate-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Return Notes / Claim Details</label>
                        <textarea v-model="form.notes" rows="4" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Any additional notes for the supplier or internal tracking..."></textarea>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
                        <div class="flex justify-between items-center bg-rose-50 p-4 rounded-xl border border-rose-100">
                            <span class="text-lg font-bold text-rose-900">Total Return Value:</span>
                            <span class="text-2xl font-black text-rose-600">${{ grandTotal.toFixed(2) }}</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-2">
                            Upon confirmation, this amount will be generated as a <strong>Supplier Credit Note</strong> and stock will be deducted from inventory.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="submit" :disabled="form.processing || form.items.length === 0" class="inline-flex justify-center rounded-xl bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all disabled:opacity-50">
                        {{ form.processing ? 'Processing...' : 'Confirm Return & Generate Credit Note' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
