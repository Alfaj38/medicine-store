<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    package: Object,
    billing_cycle: String,
    paymentRequests: Array,
    currentSubscription: Object
});

const form = useForm({
    package_id: props.package.id,
    billing_cycle: props.billing_cycle,
    gateway: 'bkash',
    sender_account_no: '',
    transaction_id: '',
    billing_cycles: 1,
    payment_date: new Date().toISOString().slice(0, 16),
    payment_proof: null
});

const submitPayment = () => {
    form.post(route('company.subscription.payment'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('sender_account_no', 'transaction_id', 'payment_proof');
            clearFile();
            Swal.fire({
                title: 'Success!',
                text: 'Your payment details have been submitted. Our team will verify and activate your subscription shortly.',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK'
            });
        }
    });
};

const previewUrl = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.payment_proof = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const clearFile = () => {
    form.payment_proof = null;
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
};

const calculateProjectedExpiry = computed(() => {
    let baseDate = new Date();
    
    // If there is an active current subscription, use its expiry date as the base
    if (props.currentSubscription && props.currentSubscription.expires_at) {
        const currentExpiry = new Date(props.currentSubscription.expires_at);
        if (currentExpiry > baseDate) {
            baseDate = currentExpiry;
        }
    }
    
    const cycles = form.billing_cycles;
    
    if (props.billing_cycle === 'yearly') {
        baseDate.setFullYear(baseDate.getFullYear() + cycles);
    } else {
        baseDate.setMonth(baseDate.getMonth() + cycles);
    }
    
    return baseDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
});

const currentExpiryFormatted = computed(() => {
    if (props.currentSubscription && props.currentSubscription.expires_at) {
        return new Date(props.currentSubscription.expires_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
    }
    return 'None';
});

const pricePerCycle = computed(() => props.billing_cycle === 'yearly' ? props.package.yearly_price : props.package.monthly_price);

const simulation = ref({
    type: 'renew',
    remaining_days: 0,
    unused_credit: 0,
    new_cost: pricePerCycle.value,
    payable_amount: pricePerCycle.value,
    effective_date: new Date().toISOString()
});

const isSimulating = ref(false);

const fetchSimulation = async () => {
    isSimulating.value = true;
    try {
        const response = await axios.post(route('company.subscription.proration'), {
            package_id: props.package.id,
            billing_cycle: props.billing_cycle,
            billing_cycles: form.billing_cycles
        });
        simulation.value = response.data;
    } catch (error) {
        console.error('Error fetching simulation', error);
    } finally {
        isSimulating.value = false;
    }
};

onMounted(() => {
    if (props.currentSubscription) {
        fetchSimulation();
    } else {
        simulation.value.payable_amount = pricePerCycle.value * form.billing_cycles;
        simulation.value.new_cost = pricePerCycle.value * form.billing_cycles;
    }
});

watch(() => form.billing_cycles, () => {
    if (props.currentSubscription) {
        fetchSimulation();
    } else {
        simulation.value.payable_amount = pricePerCycle.value * form.billing_cycles;
        simulation.value.new_cost = pricePerCycle.value * form.billing_cycles;
    }
});
</script>

<template>
    <Head title="Subscription Payment" />

    <AppLayout>
        <template #header>
            <div class="flex items-center">
                <Link :href="route('company.subscription.index')" class="text-slate-500 hover:text-emerald-600 mr-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ package.name }} Plan Payment
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Progress Tracker -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -mt-px w-full h-0.5 bg-slate-200" aria-hidden="true"></div>
                        <div class="relative flex items-center bg-white pr-4">
                            <span class="h-8 w-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm ring-4 ring-white">1</span>
                            <span class="ml-3 font-semibold text-slate-900 text-sm hidden sm:block">Select Plan</span>
                        </div>
                        <div class="relative flex items-center bg-white px-4">
                            <span class="h-8 w-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm ring-4 ring-white">2</span>
                            <span class="ml-3 font-semibold text-slate-900 text-sm hidden sm:block">Submit Details</span>
                        </div>
                        <div class="relative flex items-center bg-white px-4">
                            <span class="h-8 w-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm ring-4 ring-white">3</span>
                            <span class="ml-3 font-medium text-slate-500 text-sm hidden sm:block">Verification</span>
                        </div>
                        <div class="relative flex items-center bg-white pl-4">
                            <span class="h-8 w-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm ring-4 ring-white">4</span>
                            <span class="ml-3 font-medium text-slate-500 text-sm hidden sm:block">Complete</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Payment Instructions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 text-pink-600 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2v-4.5c0-.83-.67-1.5-1.5-1.5S10 10.67 10 11.5V16H8V8h2v2.12c.51-.71 1.34-1.12 2.25-1.12 1.52 0 2.75 1.23 2.75 2.75V16z"/></svg>
                            Payment Instructions
                        </h3>
                        
                        <div class="border-2 border-pink-100 border-dashed rounded-xl p-6 bg-pink-50/50">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">bKash Personal Account</p>
                                        <div class="flex items-center mt-1">
                                            <span class="text-2xl font-black text-slate-900">01812345678</span>
                                            <button type="button" class="ml-3 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded hover:bg-emerald-100 transition-colors">Copy</button>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Account Name</p>
                                        <p class="text-base font-bold text-slate-800">MediSaas Software</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Payable Amount</p>
                                        <p class="text-xl font-bold text-pink-600">
                                            <span v-if="isSimulating" class="animate-pulse bg-pink-200 h-6 w-24 rounded inline-block"></span>
                                            <span v-else>{{ formatCurrency(simulation.payable_amount) }}</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Payment Purpose</p>
                                        <p class="text-base font-bold text-slate-800 capitalize">{{ simulation.type }} {{ package.name }} Subscription ({{ billing_cycle }}) x {{ form.billing_cycles }}</p>
                                    </div>
                                </div>
                                <div class="mt-6 sm:mt-0 bg-white p-3 rounded-xl border border-slate-200 shadow-sm text-center shrink-0">
                                    <div class="w-40 sm:w-48 h-auto min-h-[160px] bg-white flex items-center justify-center rounded-lg border-2 border-slate-200 mb-2 overflow-hidden p-1">
                                        <img v-if="form.gateway === 'bkash'" src="/images/bkash-qr.jpg" alt="bKash QR Code" class="w-full h-auto max-h-[250px] object-contain" />
                                        <img v-else-if="form.gateway === 'nagad'" src="/images/nagad-qr.jpg" alt="Nagad QR Code" class="w-full h-auto max-h-[250px] object-contain" />
                                        <div v-else class="flex flex-col items-center justify-center text-slate-400 py-8">
                                            <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            <span class="text-xs font-bold mt-2">Bank Transfer</span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600">Scan to Pay</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-start bg-blue-50 text-blue-800 p-4 rounded-xl text-sm">
                            <svg class="w-5 h-5 mr-2 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p><strong>Important:</strong> Please send the exact amount and keep the transaction reference number (TrxID) handy. You will need it to verify your payment.</p>
                        </div>
                    </div>

                    <!-- Submit Form -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Submit Payment Details</h3>
                        <p class="text-sm text-slate-500 mb-6">After payment, please submit your payment details below.</p>
                        
                        <form @submit.prevent="submitPayment" class="space-y-5">
                            
                            <!-- Billing Cycles Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Advance Payment (Cycles)</label>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden">
                                        <button type="button" @click="form.billing_cycles > 1 ? form.billing_cycles-- : null" 
                                                class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold transition-colors border-r border-slate-300">
                                            -
                                        </button>
                                        <input type="number" v-model.number="form.billing_cycles" min="1" max="60" 
                                               class="w-16 text-center border-none focus:ring-0 sm:text-sm font-bold text-slate-800 py-2">
                                        <button type="button" @click="form.billing_cycles < 60 ? form.billing_cycles++ : null" 
                                                class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold transition-colors border-l border-slate-300">
                                            +
                                        </button>
                                    </div>
                                    <span class="text-sm text-slate-500 font-medium">
                                        {{ billing_cycle === 'yearly' ? 'Year(s)' : 'Month(s)' }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 p-3 bg-emerald-50 border border-emerald-100 rounded-lg">
                                    <div class="flex justify-between items-center text-sm mb-1">
                                        <span class="text-emerald-700 font-medium">Current Expiry:</span>
                                        <span class="text-emerald-900 font-bold">{{ currentExpiryFormatted }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-emerald-700 font-medium">Projected Expiry:</span>
                                        <span class="text-emerald-900 font-bold">{{ calculateProjectedExpiry }}</span>
                                    </div>
                                </div>

                                <!-- Simulation Summary Box -->
                                <div v-if="simulation.type !== 'renew'" class="mt-4 border rounded-xl overflow-hidden shadow-sm" :class="simulation.type === 'upgrade' ? 'border-indigo-200' : 'border-amber-200'">
                                    <div class="px-4 py-2 flex items-center font-bold text-sm" :class="simulation.type === 'upgrade' ? 'bg-indigo-50 text-indigo-800' : 'bg-amber-50 text-amber-800'">
                                        <svg v-if="simulation.type === 'upgrade'" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        <svg v-else class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                        Plan {{ simulation.type === 'upgrade' ? 'Upgrade' : 'Downgrade' }} Summary
                                    </div>
                                    <div class="p-4 bg-white text-sm space-y-2 relative">
                                        <div v-if="isSimulating" class="absolute inset-0 bg-white/50 flex items-center justify-center backdrop-blur-[1px] z-10">
                                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                        
                                        <template v-if="simulation.type === 'upgrade'">
                                            <div class="flex justify-between text-slate-600">
                                                <span>Remaining Days (Old Plan):</span>
                                                <span class="font-bold text-slate-800">{{ simulation.remaining_days }} days</span>
                                            </div>
                                            <div class="flex justify-between text-emerald-600">
                                                <span>Unused Value (Credit):</span>
                                                <span class="font-bold">- {{ formatCurrency(simulation.unused_credit) }}</span>
                                            </div>
                                            <div class="flex justify-between text-slate-600">
                                                <span>New Plan Cost:</span>
                                                <span class="font-bold text-slate-800">+ {{ formatCurrency(simulation.new_cost) }}</span>
                                            </div>
                                            <div class="pt-2 mt-2 border-t border-slate-100 flex justify-between text-indigo-700">
                                                <span class="font-bold">Total Payable Now:</span>
                                                <span class="font-black text-lg">{{ formatCurrency(simulation.payable_amount) }}</span>
                                            </div>
                                            <div class="mt-2 text-xs text-slate-500 flex items-start">
                                                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Your upgrade will take effect immediately upon admin approval.</span>
                                            </div>
                                        </template>

                                        <template v-if="simulation.type === 'downgrade'">
                                            <div class="flex justify-between text-slate-600">
                                                <span>New Plan Cost:</span>
                                                <span class="font-bold text-slate-800">{{ formatCurrency(simulation.new_cost) }}</span>
                                            </div>
                                            <div class="pt-2 mt-2 border-t border-slate-100 flex justify-between text-amber-700">
                                                <span class="font-bold">Total Payable Now:</span>
                                                <span class="font-black text-lg">{{ formatCurrency(simulation.payable_amount) }}</span>
                                            </div>
                                            <div class="mt-2 text-xs text-slate-500 flex items-start bg-amber-50 p-2 rounded text-amber-800 border border-amber-100">
                                                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Downgrade is scheduled for <strong>{{ new Date(simulation.effective_date).toLocaleDateString() }}</strong>. Your current plan remains active until then.</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-xs text-red-500 mt-1" v-if="form.errors.billing_cycles">{{ form.errors.billing_cycles }}</p>
                            </div>

                            <!-- Payment Method Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" v-model="form.gateway" value="bkash" class="peer sr-only">
                                        <div class="text-center px-3 py-2 rounded-lg border-2 border-slate-200 peer-checked:border-pink-500 peer-checked:bg-pink-50 hover:bg-slate-50 transition-all">
                                            <span class="font-bold text-slate-700 peer-checked:text-pink-600">bKash</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" v-model="form.gateway" value="nagad" class="peer sr-only">
                                        <div class="text-center px-3 py-2 rounded-lg border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-slate-50 transition-all">
                                            <span class="font-bold text-slate-700 peer-checked:text-orange-600">Nagad</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" v-model="form.gateway" value="bank" class="peer sr-only">
                                        <div class="text-center px-3 py-2 rounded-lg border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-slate-50 transition-all">
                                            <span class="font-bold text-slate-700 peer-checked:text-blue-600">Bank</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Sender Account Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="text" v-model="form.sender_account_no" class="pl-10 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. 017XXXXXXXX" required>
                                </div>
                                <p class="text-xs text-red-500 mt-1" v-if="form.errors.sender_account_no">{{ form.errors.sender_account_no }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Transaction Reference No. (TrxID)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    </div>
                                    <input type="text" v-model="form.transaction_id" class="pl-10 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm uppercase" placeholder="e.g. 9F3X7D2L8N" required>
                                </div>
                                <p class="text-xs text-red-500 mt-1" v-if="form.errors.transaction_id">{{ form.errors.transaction_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Date & Time</label>
                                <input type="datetime-local" v-model="form.payment_date" class="block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                <p class="text-xs text-red-500 mt-1" v-if="form.errors.payment_date">{{ form.errors.payment_date }}</p>
                            </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Upload Payment Screenshot (Optional)</label>
                                    
                                    <!-- Upload Area -->
                                    <div v-if="!form.payment_proof" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:bg-slate-50 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-slate-600 justify-center">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                                    <span>Upload a file</span>
                                                    <input type="file" class="sr-only" @change="handleFileUpload" accept="image/jpeg, image/png">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Image Preview Area -->
                                    <div v-else class="mt-1 relative border-2 border-slate-200 rounded-xl overflow-hidden group">
                                        <div class="w-full h-48 bg-slate-100 flex items-center justify-center">
                                            <img :src="previewUrl" alt="Payment Proof Preview" class="max-h-full max-w-full object-contain" />
                                        </div>
                                        <div class="absolute inset-0 bg-slate-900 bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button" @click="clearFile" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors shadow-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <p class="text-xs text-red-500 mt-1" v-if="form.errors.payment_proof">{{ form.errors.payment_proof }}</p>
                                </div>

                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                                <button type="button" @click="form.reset('sender_account_no', 'transaction_id', 'payment_proof')" class="px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    Reset
                                </button>
                                <button type="submit" :disabled="form.processing" class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 transition-colors">
                                    Submit for Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Requests History -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-slate-900">Your Payment Requests</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-white">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Method & TrxID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr v-for="request in paymentRequests" :key="request.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-bold text-slate-900 uppercase">{{ request.gateway }}</div>
                                            <div class="text-sm text-slate-500 ml-2">{{ request.transaction_id }}</div>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">Sender: {{ request.sender_account_no || 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900">{{ formatCurrency(request.amount) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ new Date(request.created_at).toLocaleDateString() }}</div>
                                        <div class="text-xs text-slate-500">{{ new Date(request.created_at).toLocaleTimeString() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="request.status === 'pending'" class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 text-amber-800">
                                            Pending Review
                                        </span>
                                        <span v-else-if="request.status === 'success'" class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-100 text-emerald-800">
                                            Approved
                                        </span>
                                        <span v-else-if="request.status === 'failed'" class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                        
                                        <div v-if="request.status === 'failed' && request.rejection_reason" class="text-xs text-red-600 mt-1 font-semibold max-w-[200px] truncate" :title="request.rejection_reason">
                                            Reason: {{ request.rejection_reason }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="paymentRequests.length === 0">
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            No payment requests found.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
