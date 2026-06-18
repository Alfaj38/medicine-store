<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    medicine: Object,
});

const isEdit = !!props.medicine;

const form = useForm({
    name: props.medicine?.name || '',
    generic_name: props.medicine?.generic_name || '',
    type: props.medicine?.type || '',
    strength: props.medicine?.strength || '',
    company_name: props.medicine?.company_name || '',
    barcode: props.medicine?.barcode || '',
    buy_price: props.medicine?.buy_price || 0,
    sell_price: props.medicine?.sell_price || 0,
    vat_percent: props.medicine?.vat_percent || 0,
    reorder_level: props.medicine?.reorder_level || 10,
    is_active: props.medicine ? !!props.medicine.is_active : true,
});

const submit = () => {
    if (isEdit) {
        form.put(route('medicines.update', props.medicine.id));
    } else {
        form.post(route('medicines.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Medicine' : 'Add Medicine'" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-500 shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <span class="font-bold text-xl tracking-tight text-slate-800">MediSaaS</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('medicines.index')" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm inline-flex items-center gap-1 mb-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Medicines
                </Link>
                <h1 class="text-3xl font-bold text-slate-900">{{ isEdit ? 'Edit Medicine' : 'Add New Medicine' }}</h1>
                <p class="mt-2 text-sm text-slate-500">Update the global catalog information for this medicine.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                        
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Brand Name</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Napa Extra">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <!-- Generic Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Generic Name</label>
                            <input v-model="form.generic_name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Paracetamol + Caffeine">
                        </div>

                        <!-- Type & Strength -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Type / Dosage Form</label>
                            <select v-model="form.type" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option value="">Select Type</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Capsule">Capsule</option>
                                <option value="Syrup">Syrup</option>
                                <option value="Ointment">Ointment</option>
                                <option value="Injection">Injection</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Strength</label>
                            <input v-model="form.strength" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. 500mg + 65mg">
                        </div>

                        <!-- Company -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Manufacturer / Company</label>
                            <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. Beximco Pharmaceuticals Ltd.">
                        </div>

                        <!-- Barcode -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Barcode</label>
                            <div class="mt-1 flex rounded-xl shadow-sm">
                                <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 sm:text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                                <input v-model="form.barcode" type="text" class="block w-full min-w-0 flex-1 rounded-none rounded-r-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Scan or type barcode">
                            </div>
                            <p v-if="form.errors.barcode" class="mt-1 text-sm text-red-500">{{ form.errors.barcode }}</p>
                        </div>

                        <hr class="sm:col-span-2 my-2 border-slate-100">

                        <!-- Pricing -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Buy Price</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input v-model="form.buy_price" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pl-7 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Sell Price</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input v-model="form.sell_price" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pl-7 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">VAT (%)</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <input v-model="form.vat_percent" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pr-8 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-slate-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Reorder Level (Alert)</label>
                            <input v-model="form.reorder_level" type="number" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2 pt-4">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Active Medicine (Available for POS)</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                        <Link :href="route('medicines.index')" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Medicine' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
