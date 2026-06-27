<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.coupons.index')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </Link>
                <span>Create Coupon Campaign</span>
            </div>
        </template>

        <div class="max-w-4xl mx-auto">
            
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full -z-10"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-500 rounded-full -z-10 transition-all duration-300" :style="`width: ${(currentStep - 1) / (totalSteps - 1) * 100}%`"></div>
                    
                    <div v-for="step in totalSteps" :key="step" class="flex flex-col items-center gap-2 bg-slate-50 px-2 cursor-pointer" @click="currentStep = step">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2"
                            :class="step <= currentStep ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' : 'bg-white border-slate-200 text-slate-400'">
                            {{ step }}
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-3 px-1">
                    <span class="text-xs font-bold" :class="currentStep >= 1 ? 'text-indigo-900' : 'text-slate-400'">Basic Info</span>
                    <span class="text-xs font-bold" :class="currentStep >= 2 ? 'text-indigo-900' : 'text-slate-400'">Discount Rules</span>
                    <span class="text-xs font-bold" :class="currentStep >= 3 ? 'text-indigo-900' : 'text-slate-400'">Context & Packages</span>
                    <span class="text-xs font-bold" :class="currentStep >= 4 ? 'text-indigo-900' : 'text-slate-400'">Limits</span>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                
                <!-- STEP 1: Basic -->
                <div v-show="currentStep === 1" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-xl font-black text-slate-800">Basic Information</h2>
                        <p class="text-sm text-slate-500 mt-1">What is this coupon for?</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Coupon Name</label>
                        <input v-model="form.name" type="text" placeholder="e.g. Black Friday Special" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm">
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Coupon Code</label>
                        <input v-model="form.code" type="text" placeholder="e.g. BLACKFRIDAY50" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm uppercase">
                        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Customers will enter this code at checkout</p>
                        <p v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Campaign (Optional)</label>
                        <select v-model="form.campaign_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm">
                            <option value="">No Campaign</option>
                            <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                        <textarea v-model="form.description" rows="3" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm" placeholder="Internal notes about this coupon..."></textarea>
                    </div>
                </div>

                <!-- STEP 2: Discount Rules -->
                <div v-show="currentStep === 2" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-xl font-black text-slate-800">Discount Rules</h2>
                        <p class="text-sm text-slate-500 mt-1">How much discount will the user receive?</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-colors" :class="form.discount_type === 'percentage' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" v-model="form.discount_type" value="percentage" class="text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <div>
                                <div class="font-bold text-slate-800 text-sm">Percentage</div>
                                <div class="text-xs text-slate-500">e.g. 20% off total</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-colors" :class="form.discount_type === 'fixed' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" v-model="form.discount_type" value="fixed" class="text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <div>
                                <div class="font-bold text-slate-800 text-sm">Fixed Amount</div>
                                <div class="text-xs text-slate-500">e.g. ৳500 off total</div>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Discount Value</label>
                        <div class="relative">
                            <input v-model="form.discount_value" type="number" step="0.01" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-12 pr-4 py-3 text-lg font-black text-slate-800">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">
                                {{ form.discount_type === 'percentage' ? '%' : '৳' }}
                            </span>
                        </div>
                        <p v-if="form.errors.discount_value" class="text-red-500 text-xs mt-1">{{ form.errors.discount_value }}</p>
                    </div>

                    <div v-if="form.discount_type === 'percentage'">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Maximum Discount Cap (Optional)</label>
                        <div class="relative">
                            <input v-model="form.max_discount_amount" type="number" step="0.01" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-8 pr-4 py-3 text-sm">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">৳</span>
                        </div>
                        <p class="text-[10px] text-slate-500 font-bold mt-1">Leave blank for unlimited discount</p>
                    </div>
                </div>

                <!-- STEP 3: Context & Packages -->
                <div v-show="currentStep === 3" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-xl font-black text-slate-800">Restrictions & Context</h2>
                        <p class="text-sm text-slate-500 mt-1">Who can use this coupon and on what?</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Applicable Subscription Context</label>
                        <select v-model="form.subscription_type" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm">
                            <option value="all">Any Context (New, Renewal, Upgrade)</option>
                            <option value="new_subscription">Only New Subscriptions (First time purchase)</option>
                            <option value="renewal">Only Renewals</option>
                            <option value="upgrade">Only Upgrades</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Purchase Requirement (Optional)</label>
                        <div class="relative">
                            <input v-model="form.minimum_purchase" type="number" step="0.01" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-8 pr-4 py-3 text-sm">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">৳</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Applicable Packages (Leave empty for All)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label v-for="pkg in packages" :key="pkg.id" class="flex items-center gap-2 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" v-model="form.packages" :value="pkg.id" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">{{ pkg.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Limits -->
                <div v-show="currentStep === 4" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-xl font-black text-slate-800">Limits & Expiry</h2>
                        <p class="text-sm text-slate-500 mt-1">When does it expire and how many times can it be used?</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Start Date (Optional)</label>
                            <input v-model="form.start_date" type="datetime-local" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Expire Date (Optional)</label>
                            <input v-model="form.expire_date" type="datetime-local" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Max Uses Total (Optional)</label>
                            <input v-model="form.max_uses_total" type="number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm">
                            <p class="text-[10px] text-slate-500 font-bold mt-1">Total times this can be used globally</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Max Uses Per Pharmacy (Optional)</label>
                            <input v-model="form.max_uses_per_company" type="number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm" placeholder="e.g. 1">
                            <p class="text-[10px] text-slate-500 font-bold mt-1">Leave blank for unlimited per tenant</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex gap-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_auto_apply" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                            <span class="text-sm font-bold text-slate-700">Auto Apply at Checkout</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_hidden" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                            <span class="text-sm font-bold text-slate-700">Hidden from Users</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                        <select v-model="form.status" class="w-full max-w-xs rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm font-bold">
                            <option value="active">Active Now</option>
                            <option value="scheduled">Scheduled for Later</option>
                            <option value="disabled">Disabled</option>
                        </select>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-10 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="currentStep--" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-colors text-sm" :class="{'invisible': currentStep === 1}">
                        ← Back
                    </button>
                    
                    <button v-if="currentStep < totalSteps" type="button" @click="currentStep++" class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all text-sm">
                        Continue →
                    </button>
                    
                    <button v-if="currentStep === totalSteps" type="submit" :disabled="form.processing" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all text-sm flex items-center gap-2">
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Create Coupon Now
                    </button>
                </div>
            </form>

        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

const props = defineProps({
    campaigns: Array,
    packages: Array,
});

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    name: '',
    code: '',
    description: '',
    campaign_id: '',
    
    discount_type: 'percentage',
    discount_value: '',
    max_discount_amount: '',
    
    subscription_type: 'all',
    minimum_purchase: '',
    packages: [],
    business_types: [],
    
    start_date: '',
    expire_date: '',
    max_uses_total: '',
    max_uses_per_company: '',
    
    is_auto_apply: false,
    is_hidden: false,
    status: 'active',
});

const submit = () => {
    form.post(route('admin.coupons.store'));
};
</script>
