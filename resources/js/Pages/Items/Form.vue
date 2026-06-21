<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    item: Object,
    itemTypes: Array,
    categories: Array,
    uoms: Array,
});

const isEdit = !!props.item;

// Helper to get prices
const getPrice = (type) => {
    if (!props.item || !props.item.prices) return 0;
    const price = props.item.prices.find(p => p.price_type === type);
    return price ? price.price : 0;
};

const form = useForm({
    item_type_id: props.item?.item_type_id || (props.itemTypes.length > 0 ? props.itemTypes[0].id : ''),
    name: props.item?.name || '',
    sku: props.item?.sku || '',
    barcode: props.item?.barcode || '',
    category_id: props.item?.category_id || '',
    uom_id: props.item?.uom_id || (props.uoms.length > 0 ? props.uoms[0].id : ''),
    manufacturer_name: props.item?.manufacturer?.name || '',
    buy_price: getPrice('Purchase'),
    sell_price: getPrice('Retail'),
    
    // Inventory
    inventory_type: props.item?.inventory_type || 'Stock Item',
    track_batch: props.item ? !!props.item.track_batch : true,
    track_expiry: props.item ? !!props.item.track_expiry : true,
    track_serial: props.item ? !!props.item.track_serial : false,
    is_active: props.item ? !!props.item.is_active : true,

    // Medicine specifics
    generic_name: props.item?.medicine_details?.generic_name || '',
    strength: props.item?.medicine_details?.strength || '',
    dosage_form: props.item?.medicine_details?.dosage_form || '',
});

// Computed property to check if current selected type is 'Medicine'
const isMedicineType = computed(() => {
    const selectedType = props.itemTypes.find(t => t.id === form.item_type_id);
    return selectedType && selectedType.name === 'Medicine';
});

// Computed property to filter categories by the selected item type
// We also include categories that have no item_type_id (null) so we don't hide existing data
const filteredCategories = computed(() => {
    return props.categories.filter(c => !c.item_type_id || c.item_type_id === form.item_type_id);
});

const submit = () => {
    if (isEdit) {
        form.put(route('items.update', props.item.id));
    } else {
        form.post(route('items.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Item' : 'Add Item'" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar />

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('items.index')" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm inline-flex items-center gap-1 mb-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Catalog
                </Link>
                <h1 class="text-3xl font-bold text-slate-900">{{ isEdit ? 'Edit Item' : 'Add New Item' }}</h1>
                <p class="mt-2 text-sm text-slate-500">Add a new item, medicine, or service to the Unified Catalog.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                        
                        <!-- Item Classification -->
                        <div class="sm:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-100 flex gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Item Type</label>
                                <select v-model="form.item_type_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-white">
                                    <option v-for="t in itemTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Inventory Behavior</label>
                                <select v-model="form.inventory_type" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-white">
                                    <option value="Stock Item">Stock Item (Track Quantity)</option>
                                    <option value="Non-Stock Item">Non-Stock Item</option>
                                    <option value="Service">Service (e.g. Delivery, Checkup)</option>
                                    <option value="Asset">Asset</option>
                                </select>
                            </div>
                        </div>

                        <!-- Basic Details -->
                        <div class="sm:col-span-2 mt-2">
                            <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Basic Information</h3>
                        </div>

                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Item Name</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Napa Extra 500mg or Baby Diaper">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <!-- Dynamic Medicine Fields -->
                        <template v-if="isMedicineType">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Generic Name</label>
                                <input v-model="form.generic_name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Paracetamol">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Dosage Form</label>
                                <select v-model="form.dosage_form" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="">Select Type</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Capsule">Capsule</option>
                                    <option value="Syrup">Syrup</option>
                                    <option value="Injection">Injection</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Strength</label>
                                <input v-model="form.strength" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. 500mg">
                            </div>
                        </template>

                        <!-- Identifiers -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Barcode (POS)</label>
                            <input v-model="form.barcode" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Scan barcode">
                            <p v-if="form.errors.barcode" class="mt-1 text-sm text-red-500">{{ form.errors.barcode }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">SKU (Optional)</label>
                            <input v-model="form.sku" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. NAPA-500-TAB">
                        </div>

                        <!-- Organization -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Category</label>
                            <select v-model="form.category_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option value="">No Category</option>
                                <option v-for="c in filteredCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Manufacturer / Brand</label>
                            <input v-model="form.manufacturer_name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Beximco">
                        </div>

                        <div class="sm:col-span-2 mt-4">
                            <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Pricing & Units</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Purchase Price</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-slate-500 sm:text-sm">$</span></div>
                                <input v-model="form.buy_price" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pl-7 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Retail Price (Selling)</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-slate-500 sm:text-sm">$</span></div>
                                <input v-model="form.sell_price" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pl-7 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Base Unit of Measure (UOM)</label>
                            <select v-model="form.uom_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option v-for="u in uoms" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                        </div>

                        <template v-if="form.inventory_type === 'Stock Item'">
                            <div class="sm:col-span-2 mt-4">
                                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Inventory Tracking Rules</h3>
                            </div>
                            
                            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" v-model="form.track_batch" class="text-emerald-600 focus:ring-emerald-500 rounded">
                                    <span class="text-sm font-medium text-slate-700">Track Batches</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" v-model="form.track_expiry" class="text-emerald-600 focus:ring-emerald-500 rounded">
                                    <span class="text-sm font-medium text-slate-700">Track Expiry Dates</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" v-model="form.track_serial" class="text-emerald-600 focus:ring-emerald-500 rounded">
                                    <span class="text-sm font-medium text-slate-700">Track Serial Numbers</span>
                                </label>
                            </div>
                        </template>

                        <!-- Status -->
                        <div class="sm:col-span-2 pt-4 border-t mt-4">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Active Status (Visible in POS)</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                        <Link :href="route('items.index')" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
