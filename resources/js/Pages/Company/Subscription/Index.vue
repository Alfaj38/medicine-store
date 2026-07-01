<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    plans: Array,
    currentSubscription: Object,
    monthlyBill: [Number, String],
    dueBill: [Number, String],
    paymentHistory: Array,
});

const page = usePage();

const form = useForm({
    package_id: null,
    billing_cycle: null,
});

const hasPendingPayment = computed(() => {
    return props.paymentHistory && props.paymentHistory.some(p => p.status === 'pending');
});

const upgrade = (plan, billing_cycle) => {
    form.package_id = plan.id;
    form.billing_cycle = billing_cycle;
    
    // Redirect to checkout page using Inertia GET
    form.get(route('company.subscription.checkout'), {
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
};

const formatFeature = (feature) => {
    if (typeof feature === 'string') return feature;
    if (!feature || !feature.feature_code) return null;

    // Convert snake_case code to Title Case label
    const label = feature.feature_code
        .replace(/_/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());

    if (feature.limit !== null && feature.limit !== undefined) {
        return `${feature.limit} ${label}`;
    }
    return label;
};

const visibleFeatures = (features) => {
    if (!features || features.length === 0) {
        return ['Unlimited Sales', 'Inventory Tracking', 'Daily Reports', 'Basic Support'];
    }
    return features
        .filter(f => f.is_enabled || (f.limit !== null && f.limit !== undefined))
        .map(formatFeature)
        .filter(Boolean);
};
</script>

<template>
    <Head title="Subscription Management" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Subscription & Billing
            </h2>
        </template>

        <div class="py-12">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                
                <!-- Current Subscription Status -->
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 mb-8 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Current Plan Status</h3>
                            <p class="text-sm text-slate-500">Manage your billing and view your current features.</p>
                        </div>
                        <div class="px-4 py-2 rounded-xl" :class="currentSubscription?.status === 'trial' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'">
                            <span class="font-bold text-sm uppercase tracking-wide">{{ currentSubscription?.status || 'Unknown' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-medium text-slate-500 mb-1">Active Plan</p>
                            <p class="font-bold text-slate-900">{{ currentSubscription?.package?.name || 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-medium text-slate-500 mb-1">Billing Cycle</p>
                            <p class="font-bold text-slate-900 capitalize">{{ currentSubscription?.billing_cycle || 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-medium text-slate-500 mb-1">Expires At</p>
                            <p class="font-bold text-slate-900">
                                {{ currentSubscription?.expires_at ? new Date(currentSubscription.expires_at).toLocaleDateString() : 'N/A' }}
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-medium text-slate-500 mb-1">Monthly Bill</p>
                            <p class="font-bold text-slate-900">
                                {{ monthlyBill > 0 ? formatCurrency(monthlyBill) : 'N/A' }}
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border" :class="dueBill > 0 ? 'border-red-200 bg-red-50' : 'border-slate-100'">
                            <p class="text-sm font-medium mb-1" :class="dueBill > 0 ? 'text-red-600' : 'text-slate-500'">Outstanding Balance</p>
                            <p class="font-bold text-lg" :class="dueBill > 0 ? 'text-red-700' : 'text-slate-900'">
                                {{ formatCurrency(dueBill) }}
                            </p>
                            <p v-if="dueBill > 0" class="text-xs text-red-500 mt-1 font-semibold">Payment Required</p>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 mb-8 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Payment History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr v-for="payment in paymentHistory" :key="payment.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        {{ new Date(payment.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                        {{ formatCurrency(payment.amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                              :class="{
                                                  'bg-emerald-100 text-emerald-800': payment.status === 'success' || payment.status === 'completed',
                                                  'bg-amber-100 text-amber-800': payment.status === 'pending',
                                                  'bg-red-100 text-red-800': payment.status === 'failed'
                                              }">
                                            {{ payment.status }}
                                        </span>
                                        <div v-if="payment.status === 'failed' && payment.rejection_reason" class="text-xs text-red-600 mt-1 font-semibold max-w-[200px] truncate" :title="payment.rejection_reason">
                                            Reason: {{ payment.rejection_reason }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                                        <a :href="route('company.subscription.invoice', payment.id)" class="font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Download Invoice
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="!paymentHistory || paymentHistory.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No payment history found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Available Plans -->
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-slate-900 text-center mb-2">
                        {{ currentSubscription?.package_id ? 'Upgrade Your Pharmacy' : 'Choose a Plan' }}
                    </h3>
                    <p class="text-slate-500 text-center mb-8">Choose the plan that fits your business needs.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:px-4">
                        <div v-for="plan in plans" :key="plan.id"
                             class="rounded-3xl overflow-hidden flex flex-col relative transition-all duration-300"
                             :class="currentSubscription?.package_id === plan.id
                                 ? 'bg-gradient-to-b from-emerald-50 to-white ring-4 ring-emerald-500 shadow-2xl shadow-emerald-200/50 md:scale-[1.03] z-10'
                                 : 'bg-white border border-slate-200 shadow-sm hover:shadow-lg hover:border-emerald-200'">

                            <!-- Current Plan Banner -->
                            <div v-if="currentSubscription?.package_id === plan.id" class="absolute top-0 inset-x-0">
                                <div class="bg-emerald-500 text-white text-sm font-black uppercase tracking-widest text-center py-2 flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    YOUR CURRENT PLAN
                                </div>
                            </div>

                            <!-- Most Popular Badge (only for non-current plan) -->
                            <div v-else-if="plan.name.toLowerCase().includes('pro')" class="absolute top-0 inset-x-0">
                                <div class="bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider text-center py-1">
                                    Most Popular
                                </div>
                            </div>

                            <div class="p-8 flex-1" :class="currentSubscription?.package_id === plan.id ? 'mt-4' : ''">
                                <div class="flex items-center justify-between mt-4 mb-2">
                                    <h4 class="text-xl font-bold" :class="currentSubscription?.package_id === plan.id ? 'text-emerald-700 text-2xl' : 'text-slate-900'">
                                        {{ plan.name }}
                                    </h4>
                                    <span v-if="currentSubscription?.package_id === plan.id"
                                          class="inline-flex items-center gap-1 text-sm font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 capitalize shadow-sm">
                                        {{ currentSubscription.billing_cycle }} Cycle
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 mb-6 min-h-[40px]">{{ plan.description }}</p>
                                
                                <div class="space-y-4 mb-8">
                                    <!-- Monthly -->
                                    <div class="p-4 rounded-xl border-2 transition-colors" 
                                         :class="currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'monthly'
                                             ? 'border-emerald-400 bg-emerald-50'
                                             : form.package_id === plan.id && form.billing_cycle === 'monthly'
                                                 ? 'border-emerald-500 bg-emerald-50'
                                                 : 'border-slate-100 hover:border-slate-200'">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-slate-900 capitalize">Monthly</span>
                                            <span v-if="currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'monthly'"
                                                  class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Active</span>
                                        </div>
                                        <div class="flex items-baseline">
                                            <span class="text-2xl font-black text-slate-900">৳{{ plan.monthly_price }}</span>
                                            <span class="text-slate-500 text-sm ml-1">/mo</span>
                                        </div>
                                        <button @click="upgrade(plan, 'monthly')" 
                                                class="mt-4 w-full py-2 px-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center"
                                                :disabled="hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending'"
                                                :class="(hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending')
                                                    ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                                    : currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'monthly'
                                                        ? 'bg-emerald-500 text-white hover:bg-emerald-600'
                                                        : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white'">
                                            <template v-if="hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending'">
                                                Pending Verification
                                            </template>
                                            <template v-else>
                                                {{ (currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'monthly') ? '🔄 Renew Plan' : 'Select & Pay' }}
                                            </template>
                                        </button>
                                    </div>

                                    <!-- Yearly -->
                                    <div class="p-4 rounded-xl border-2 transition-colors" 
                                         :class="currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'yearly'
                                             ? 'border-emerald-400 bg-emerald-50'
                                             : form.package_id === plan.id && form.billing_cycle === 'yearly'
                                                 ? 'border-emerald-500 bg-emerald-50'
                                                 : 'border-slate-100 hover:border-slate-200'">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-slate-900 capitalize">Yearly</span>
                                            <span v-if="currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'yearly'"
                                                  class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Active</span>
                                            <span v-else class="text-xs font-bold px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full">Save ~17%</span>
                                        </div>
                                        <div class="flex items-baseline">
                                            <span class="text-2xl font-black text-slate-900">৳{{ plan.yearly_price }}</span>
                                            <span class="text-slate-500 text-sm ml-1">/yr</span>
                                        </div>
                                        <button @click="upgrade(plan, 'yearly')" 
                                                class="mt-4 w-full py-2 px-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center"
                                                :disabled="hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending'"
                                                :class="(hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending')
                                                    ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                                    : currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'yearly'
                                                        ? 'bg-emerald-500 text-white hover:bg-emerald-600'
                                                        : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white'">
                                            <template v-if="hasPendingPayment || page.props.auth.subscription?.state === 'payment_pending'">
                                                Pending Verification
                                            </template>
                                            <template v-else>
                                                {{ (currentSubscription?.package_id === plan.id && currentSubscription?.billing_cycle === 'yearly') ? '🔄 Renew Plan' : 'Select & Pay' }}
                                            </template>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Features included:</h5>
                                    <div class="flex items-start" v-for="feat in visibleFeatures(plan.features)" :key="feat">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="text-sm text-slate-600">{{ feat }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
