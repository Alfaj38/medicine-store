<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    plans: Array,
    referral_code: String,
});

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    // Step 1: Business Profile
    company_name: '',
    company_type: '',
    branch_count: '1',
    // Step 2: Company Setup
    company_country: 'Bangladesh',
    company_currency: 'BDT',
    company_timezone: 'Asia/Dhaka',
    company_address: '',
    company_phone: '',
    // Step 3: Admin Account
    owner_name: '',
    owner_email: '',
    owner_phone: '',
    password: '',
    password_confirmation: '',
    // Step 4: Trial Plan
    plan_id: props.plans?.find(p => p.slug === 'professional')?.id || props.plans[0]?.id || '',
    billing_cycle: 'monthly',
    referral_code: props.referral_code || '',
    referral_source: '',
});

const validatingCode = ref(false);
const validCode = ref(!!props.referral_code);
const invalidCodeMsg = ref('');

let timeout = null;
watch(() => form.referral_code, (val) => {
    if (props.referral_code) return; // Locked from link
    invalidCodeMsg.value = '';
    validCode.value = false;
    
    if (!val) return;

    clearTimeout(timeout);
    timeout = setTimeout(async () => {
        validatingCode.value = true;
        try {
            const res = await axios.get('/api/promo-code/validate', { params: { code: val } });
            if (res.data.valid) {
                validCode.value = true;
            } else {
                invalidCodeMsg.value = res.data.message || 'Invalid code';
            }
        } catch (e) {
            invalidCodeMsg.value = 'Invalid code';
        }
        validatingCode.value = false;
    }, 500);
});

const nextStep = () => {
    if (currentStep.value < totalSteps) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const submit = () => {
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const businessTypes = ['Retail Pharmacy', 'Chain Pharmacy', 'Hospital Pharmacy', 'Distributor'];
const branchOptions = ['1', '2-5', '6-20', '20+'];
const referralOptions = ['Google Search', 'Facebook / Instagram', 'LinkedIn', 'Friend / Colleague', 'YouTube', 'Other'];
</script>

<template>
    <Head title="Start Free Trial - MediSaaS" />
    
    <div class="min-h-screen bg-slate-50 flex font-sans">
        
        <!-- Left Side: Value Prop (Desktop Only) -->
        <div class="hidden lg:flex w-1/3 bg-emerald-600 text-white flex-col justify-between p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:20px_20px]"></div>
            
            <div class="relative z-10">
                <Link :href="route('home')" class="flex items-center gap-2 mb-16">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-lg">
                        <span class="text-emerald-600 font-bold text-lg leading-none">+</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">MediSaaS</span>
                </Link>
                
                <h2 class="text-4xl font-black mb-6 leading-tight">Join 500+ pharmacies growing with MediSaaS.</h2>
                <ul class="space-y-4 mb-12">
                    <li class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">✓</div>
                        <span>14-day free trial</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">✓</div>
                        <span>No credit card required</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">✓</div>
                        <span>Cancel anytime</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center">✓</div>
                        <span>Setup in 5 minutes</span>
                    </li>
                </ul>
            </div>
            
            <div class="relative z-10 bg-emerald-700/50 p-6 rounded-2xl backdrop-blur-sm border border-emerald-500/30">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex -space-x-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-400 border-2 border-emerald-700 flex items-center justify-center font-bold text-xs">AM</div>
                        <div class="w-10 h-10 rounded-full bg-teal-400 border-2 border-emerald-700 flex items-center justify-center font-bold text-xs">SK</div>
                        <div class="w-10 h-10 rounded-full bg-cyan-400 border-2 border-emerald-700 flex items-center justify-center font-bold text-xs">RH</div>
                    </div>
                    <div class="text-sm font-medium">Trusted by leading pharmacies</div>
                </div>
                <div class="flex gap-1 text-amber-300 text-sm">★★★★★</div>
            </div>
        </div>

        <!-- Right Side: Wizard Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-32">
            
            <div class="w-full max-w-2xl mx-auto">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-8">
                    <Link :href="route('home')" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg leading-none">+</span>
                        </div>
                        <span class="font-bold text-2xl tracking-tight text-slate-900">MediSaaS</span>
                    </Link>
                </div>

                <!-- Progress Steps -->
                <div class="mb-12">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full -z-10"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full -z-10 transition-all duration-300" :style="`width: ${(currentStep - 1) / (totalSteps - 1) * 100}%`"></div>
                        
                        <div v-for="step in totalSteps" :key="step" class="flex flex-col items-center gap-2 bg-slate-50 px-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2"
                                :class="step < currentStep ? 'bg-emerald-500 border-emerald-500 text-white' : (step === currentStep ? 'bg-white border-emerald-500 text-emerald-600' : 'bg-white border-slate-200 text-slate-400')">
                                <span v-if="step < currentStep">✓</span>
                                <span v-else>{{ step }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 px-1">
                        <span class="text-[10px] font-semibold text-slate-500">Business</span>
                        <span class="text-[10px] font-semibold text-slate-500">Setup</span>
                        <span class="text-[10px] font-semibold text-slate-500">Admin</span>
                        <span class="text-[10px] font-semibold text-slate-500">Plan</span>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="currentStep === totalSteps ? submit() : nextStep()" class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100">
                    
                    <!-- STEP 1: Business Profile -->
                    <div v-show="currentStep === 1" class="space-y-6">
                        <div class="mb-8">
                            <h2 class="text-2xl font-black text-slate-900">Business Profile</h2>
                            <p class="text-sm text-slate-500 mt-1">Let's start with your pharmacy's basic details.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Business Name <span class="text-red-500">*</span></label>
                            <input v-model="form.company_name" type="text" :required="currentStep === 1" placeholder="Example Pharmacy Ltd." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <p v-if="form.errors.company_name" class="text-red-500 text-xs mt-1">{{ form.errors.company_name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Business Type <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label v-for="opt in businessTypes" :key="opt" class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="form.company_type === opt ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500' : 'border-slate-200 hover:border-slate-300'">
                                    <input type="radio" v-model="form.company_type" :value="opt" :required="currentStep === 1" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm font-semibold text-slate-700">{{ opt }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Number of Branches <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-4 gap-3">
                                <label v-for="opt in branchOptions" :key="opt" class="flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all" :class="form.branch_count === opt ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500' : 'border-slate-200 hover:border-slate-300'">
                                    <input type="radio" v-model="form.branch_count" :value="opt" :required="currentStep === 1" class="sr-only">
                                    <span class="text-sm font-bold" :class="form.branch_count === opt ? 'text-emerald-700' : 'text-slate-600'">{{ opt }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Company Setup -->
                    <div v-show="currentStep === 2" class="space-y-6">
                        <div class="mb-8">
                            <h2 class="text-2xl font-black text-slate-900">Company Setup</h2>
                            <p class="text-sm text-slate-500 mt-1">We'll pre-configure your tenant with these settings.</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Country</label>
                                <select v-model="form.company_country" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 bg-white">
                                    <option>Bangladesh</option>
                                    <option>India</option>
                                    <option>UAE</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Currency</label>
                                <select v-model="form.company_currency" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 bg-white">
                                    <option>BDT</option>
                                    <option>INR</option>
                                    <option>AED</option>
                                    <option>USD</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Business Address <span class="text-red-500">*</span></label>
                            <input v-model="form.company_address" type="text" :required="currentStep === 2" placeholder="Street, City, Postal Code" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Business Phone <span class="text-red-500">*</span></label>
                            <input v-model="form.company_phone" type="text" :required="currentStep === 2" placeholder="+880..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- STEP 3: Admin Account -->
                    <div v-show="currentStep === 3" class="space-y-6">
                        <div class="mb-8">
                            <h2 class="text-2xl font-black text-slate-900">Admin Account</h2>
                            <p class="text-sm text-slate-500 mt-1">Create the owner account to manage the pharmacy.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Owner Full Name <span class="text-red-500">*</span></label>
                            <input v-model="form.owner_name" type="text" :required="currentStep === 3" placeholder="John Doe" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                <input v-model="form.owner_email" type="email" :required="currentStep === 3" placeholder="john@example.com" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.owner_email" class="text-red-500 text-xs mt-1">{{ form.errors.owner_email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Mobile Number <span class="text-red-500">*</span></label>
                                <input v-model="form.owner_phone" type="text" :required="currentStep === 3" placeholder="+880..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                                <input v-model="form.password" type="password" :required="currentStep === 3" placeholder="Min. 8 characters" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                                <input v-model="form.password_confirmation" type="password" :required="currentStep === 3" placeholder="Confirm password" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Trial Plan -->
                    <div v-show="currentStep === 4" class="space-y-6">
                        <div class="mb-6 text-center">
                            <h2 class="text-2xl font-black text-slate-900">Select Your Trial Plan</h2>
                            <p class="text-sm text-slate-500 mt-1">Start your 14-day free trial. No credit card required.</p>
                        </div>
                        
                        <div class="flex justify-center gap-4 mb-6">
                            <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-4 py-2 rounded-full border border-slate-200">
                                <input type="radio" v-model="form.billing_cycle" value="monthly" class="text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-xs font-bold">Monthly Billing</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-4 py-2 rounded-full border border-slate-200">
                                <input type="radio" v-model="form.billing_cycle" value="yearly" class="text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-xs font-bold">Yearly Billing <span class="text-emerald-600">(Save 10%)</span></span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <label v-for="plan in plans" :key="plan.id" class="cursor-pointer relative">
                                <input type="radio" v-model="form.plan_id" :value="plan.id" class="peer sr-only" />
                                <div v-if="plan.slug === 'professional'" class="absolute -top-3 inset-x-0 mx-auto w-max px-2 py-0.5 bg-emerald-500 text-white text-[10px] font-bold rounded-full uppercase tracking-wider z-10 shadow-sm">Recommended</div>
                                <div class="rounded-2xl border-2 border-slate-200 p-5 hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-md transition-all text-center h-full flex flex-col justify-between relative bg-white">
                                    <div>
                                        <h4 class="font-black text-slate-900 text-lg">{{ plan.name }}</h4>
                                        <p class="text-xs text-slate-500 mt-2 font-medium">{{ plan.description }}</p>
                                    </div>
                                    <div class="mt-6">
                                        <span class="text-3xl font-black text-emerald-600">
                                            ৳{{ plan.prices.find(p => p.billing_cycle === form.billing_cycle)?.total_price || 0 }}
                                        </span>
                                        <span class="text-xs text-slate-500 font-bold block mt-1">/{{ form.billing_cycle === 'yearly' ? 'year' : 'month' }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-6 border-t border-slate-100 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Referral/Promo Code (Optional)</label>
                                <div class="relative">
                                    <input v-model="form.referral_code" type="text" placeholder="e.g. MAMUN2026" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 bg-white pr-10 uppercase" :readonly="!!props.referral_code" :class="{'bg-slate-50 text-slate-500': !!props.referral_code}">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg v-if="validatingCode" class="animate-spin w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <svg v-else-if="validCode" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <svg v-else-if="invalidCodeMsg" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                </div>
                                <p v-if="props.referral_code" class="text-[10px] text-emerald-600 font-bold mt-1">✓ Referral code applied from link</p>
                                <p v-else-if="invalidCodeMsg" class="text-[10px] text-red-500 font-bold mt-1">{{ invalidCodeMsg }}</p>
                                <p v-else-if="validCode" class="text-[10px] text-emerald-600 font-bold mt-1">✓ Code applied successfully</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">How did you hear about us? (Optional)</label>
                                <select v-model="form.referral_source" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 bg-white">
                                    <option value="">Select source...</option>
                                    <option v-for="opt in referralOptions" :key="opt" :value="opt">{{ opt }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <p v-if="form.errors.plan_id" class="text-red-500 text-xs text-center mt-2">{{ form.errors.plan_id }}</p>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center justify-between mt-10 pt-6 border-t border-slate-100">
                        <button type="button" @click="prevStep" :class="currentStep === 1 ? 'invisible' : ''" class="px-6 py-2.5 rounded-full font-bold text-slate-500 hover:bg-slate-100 transition-colors text-sm">
                            ← Back
                        </button>
                        
                        <button v-if="currentStep < totalSteps" type="submit" class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-full font-bold shadow-lg shadow-slate-900/20 transition-all text-sm flex items-center gap-2">
                            Next Step →
                        </button>
                        
                        <button v-if="currentStep === totalSteps" type="submit" :disabled="form.processing" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full font-bold shadow-lg shadow-emerald-500/30 transition-all text-sm flex items-center gap-2 disabled:opacity-70">
                            <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            {{ form.processing ? 'Starting Trial...' : 'Start 14-Day Free Trial 🎉' }}
                        </button>
                    </div>

                    <div v-if="currentStep === 1" class="text-center mt-6">
                        <span class="text-sm text-slate-500">Already have an account? </span>
                        <Link :href="route('login')" class="text-sm font-bold text-emerald-600 hover:underline">Sign In</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
