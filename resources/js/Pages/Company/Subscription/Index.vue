<script setup>
import { Head, useForm } from '@inertiajs/vue3';
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

const form = useForm({
    plan_id: null,
    price_id: null,
});

const hasPendingPayment = computed(() => {
    return props.paymentHistory && props.paymentHistory.some(p => p.status === 'pending');
});

const upgrade = (plan, price) => {
    form.plan_id = plan.id;
    form.price_id = price.id;
    
    // Redirect to checkout page using Inertia GET
    form.get(route('company.subscription.checkout'), {
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
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
                            <p class="font-bold text-slate-900">{{ currentSubscription?.plan?.name || 'N/A' }}</p>
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
                                                  'bg-green-100 text-green-800': payment.status === 'completed',
                                                  'bg-yellow-100 text-yellow-800': payment.status === 'pending',
                                                  'bg-red-100 text-red-800': payment.status === 'failed'
                                              }">
                                            {{ payment.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900 cursor-pointer">
                                        Download Invoice
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
                    <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Upgrade Your Pharmacy</h3>
                    <p class="text-slate-500 text-center mb-8">Choose the plan that fits your business needs.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative transition-all hover:shadow-lg hover:border-emerald-200">
                            <!-- Popular Badge -->
                            <div v-if="plan.name.toLowerCase().includes('pro')" class="absolute top-0 inset-x-0">
                                <div class="bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider text-center py-1">
                                    Most Popular
                                </div>
                            </div>

                            <div class="p-8 flex-1">
                                <h4 class="text-xl font-bold text-slate-900 mb-2 mt-4">{{ plan.name }}</h4>
                                <p class="text-sm text-slate-500 mb-6 min-h-[40px]">{{ plan.description }}</p>
                                
                                <div class="space-y-4 mb-8">
                                    <div v-for="price in plan.prices" :key="price.id" class="p-4 rounded-xl border-2 transition-colors" 
                                         :class="form.price_id === price.id ? 'border-emerald-500 bg-emerald-50' : 'border-slate-100 hover:border-slate-200'">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-slate-900 capitalize">{{ price.billing_cycle }}</span>
                                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 text-slate-600 rounded-md" v-if="price.billing_cycle === 'yearly'">Save 20%</span>
                                        </div>
                                        <div class="flex items-baseline">
                                            <span class="text-2xl font-black text-slate-900">৳{{ price.total_price }}</span>
                                            <span class="text-slate-500 text-sm ml-1">/{{ price.billing_cycle === 'yearly' ? 'yr' : 'mo' }}</span>
                                        </div>
                                        <button @click="upgrade(plan, price)" 
                                                class="mt-4 w-full py-2 px-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center"
                                                :disabled="hasPendingPayment || (currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id && new Date(currentSubscription?.expires_at) > new Date())"
                                                :class="(hasPendingPayment || (currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id && new Date(currentSubscription?.expires_at) > new Date())) ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white'">
                                            <template v-if="hasPendingPayment">
                                                Pending Bill
                                            </template>
                                            <template v-else>
                                                {{ (currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id && new Date(currentSubscription?.expires_at) > new Date()) ? 'Current Plan' : (currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id ? 'Renew Plan' : 'Select & Pay') }}
                                            </template>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Features included:</h5>
                                    <div class="flex items-start" v-for="feature in (plan.features || ['Unlimited Sales', 'Inventory Tracking', 'Daily Reports', 'Basic Support'])" :key="feature">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="text-sm text-slate-600">{{ feature }}</span>
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
