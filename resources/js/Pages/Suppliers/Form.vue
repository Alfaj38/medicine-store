<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    supplier: Object,
    industries: Array,
});

const isEdit = !!props.supplier;

import { ref, computed } from 'vue';

const showDropdown = ref(false);
const currentCompanyInput = ref('');

const filteredIndustries = computed(() => {
    if (!props.industries) return [];
    if (!currentCompanyInput.value) return props.industries.slice(0, 50);
    
    const query = currentCompanyInput.value.toLowerCase();
    return props.industries.filter(ind => ind.toLowerCase().includes(query)).slice(0, 50);
});

const selectIndustry = (industry) => {
    if (!form.companies.includes(industry)) {
        form.companies.push(industry);
    }
    currentCompanyInput.value = '';
    showDropdown.value = false;
};

const removeIndustry = (index) => {
    form.companies.splice(index, 1);
};

const form = useForm({
    name: props.supplier?.name || '',
    companies: props.supplier?.companies || [],
    email: props.supplier?.email || '',
    phone: props.supplier?.phone || '',
    address: props.supplier?.address || '',
    opening_balance: props.supplier?.opening_balance || 0,
    is_active: props.supplier ? !!props.supplier.is_active : true,
});

const submit = () => {
    if (isEdit) {
        form.put(route('suppliers.update', props.supplier.id));
    } else {
        form.post(route('suppliers.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Supplier' : 'Add Supplier'" />

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

        <main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('suppliers.index')" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm inline-flex items-center gap-1 mb-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Suppliers
                </Link>
                <h1 class="text-3xl font-bold text-slate-900">{{ isEdit ? 'Edit Supplier' : 'Add New Supplier' }}</h1>
                <p class="mt-2 text-sm text-slate-500">Manage supplier contact details and initial balances.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                        
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Contact Person Name</label>
                            <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. John Doe">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <!-- Companies -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Associated Companies</label>
                            
                            <!-- Selected Companies Chips -->
                            <div class="flex flex-wrap gap-2 mb-2" v-if="form.companies.length > 0">
                                <span v-for="(company, idx) in form.companies" :key="idx" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                    {{ company }}
                                    <button @click.prevent="removeIndustry(idx)" class="hover:text-emerald-900 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            </div>

                            <div class="relative">
                                <input 
                                    v-model="currentCompanyInput" 
                                    @focus="showDropdown = true"
                                    @blur="showDropdown = false"
                                    type="text" 
                                    class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" 
                                    placeholder="Search and add company...">
                                
                                <div v-if="showDropdown && filteredIndustries.length > 0" class="absolute z-[100] w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto left-0">
                                    <ul class="py-1 text-sm text-slate-700">
                                        <li 
                                            v-for="industry in filteredIndustries" 
                                            :key="industry"
                                            @mousedown.prevent="selectIndustry(industry)"
                                            class="px-4 py-2.5 hover:bg-emerald-50 cursor-pointer font-medium transition-colors"
                                        >
                                            {{ industry }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p v-if="form.errors.companies" class="mt-1 text-sm text-red-500">{{ form.errors.companies }}</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Phone Number</label>
                            <input v-model="form.phone" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. +8801700000000">
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email Address</label>
                            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. contact@supplier.com">
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Physical Address</label>
                            <textarea v-model="form.address" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Full address..."></textarea>
                            <p v-if="form.errors.address" class="mt-1 text-sm text-red-500">{{ form.errors.address }}</p>
                        </div>

                        <hr class="sm:col-span-2 my-2 border-slate-100">

                        <!-- Opening Balance -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Opening Balance (Payable)</label>
                            <div class="relative mt-1 rounded-xl shadow-sm max-w-xs">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input v-model="form.opening_balance" type="number" step="0.01" class="block w-full rounded-xl border-slate-300 pl-7 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0.00">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Amount you already owe to this supplier before using the system.</p>
                            <p v-if="form.errors.opening_balance" class="mt-1 text-sm text-red-500">{{ form.errors.opening_balance }}</p>
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2 pt-4">
                            <label class="flex items-center cursor-pointer group w-max">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Active Supplier</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                        <Link :href="route('suppliers.index')" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Supplier' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</template>
