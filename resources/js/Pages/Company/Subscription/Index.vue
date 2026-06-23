<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    plans: Array,
    currentSubscription: Object
});

const form = useForm({
    plan_id: null,
    price_id: null,
});

const upgrade = (plan, price) => {
    Swal.fire({
        title: 'Confirm Upgrade',
        text: `You are about to upgrade to the ${plan.name} plan (${price.billing_cycle}) for ৳${price.price}. A mock payment will be processed.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Process Payment',
    }).then((result) => {
        if (result.isConfirmed) {
            form.plan_id = plan.id;
            form.price_id = price.id;
            form.post(route('company.subscription.upgrade'), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Your subscription has been upgraded. If you were referred, your partner just received their commission!',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
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

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                            <span class="text-2xl font-black text-slate-900">৳{{ price.price }}</span>
                                            <span class="text-slate-500 text-sm ml-1">/{{ price.billing_cycle === 'yearly' ? 'yr' : 'mo' }}</span>
                                        </div>
                                        <button @click="upgrade(plan, price)" 
                                                class="mt-4 w-full py-2 px-4 rounded-lg font-bold text-sm transition-colors"
                                                :class="(currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id) ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white'">
                                            {{ (currentSubscription?.plan_id === plan.id && currentSubscription?.plan_price_id === price.id) ? 'Current Plan' : 'Select & Pay' }}
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
