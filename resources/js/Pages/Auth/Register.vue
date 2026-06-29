<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    packages: Array,
    referral_code: String,
});

const currentStep = ref(1);
const showMobileForm = ref(false);

const totalSteps = 4;

const form = useForm({
    // Step 1: Business Profile
    company_name: '',
    company_type: 'Retail Pharmacy',
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
    package_id: props.packages?.find(p => p.slug === 'professional')?.id || props.packages?.[0]?.id || '',
    billing_cycle: 'monthly',
    referral_code: props.referral_code || '',
    referral_source: '',
    coupon_code: '',
});

// Computed property for smart package recommendation
const recommendedPackage = computed(() => {
    if (!props.packages || props.packages.length === 0) return null;
    
    const type = form.company_type;
    const branches = form.branch_count;
    let targetCode = 'professional';
    
    if (type === 'Retail Pharmacy' && branches === '1') {
        targetCode = 'starter';
    } else if (type === 'Retail Pharmacy' && branches === '2-5') {
        targetCode = 'professional';
    } else if (type === 'Chain Pharmacy') {
        targetCode = branches === '1' || branches === '2-5' ? 'professional' : 'business';
    } else if (type === 'Hospital Pharmacy' || type === 'Distributor') {
        targetCode = 'enterprise';
    }
    
    return props.packages.find(p => p.code === targetCode) || props.packages[1] || props.packages[0];
});

const recommendedPackageName = computed(() => {
    return recommendedPackage.value ? `${recommendedPackage.value.name} Plan` : 'Starter Plan';
});

const recommendedFeatures = computed(() => {
    if (!recommendedPackage.value || !recommendedPackage.value.features) return [];
    return recommendedPackage.value.features
        .filter(f => f.is_enabled || f.limit !== null)
        .slice(0, 6);
});

// Computed property for the sidebar display (shows recommended package in steps 1-3, selected package in step 4)
const displayPackage = computed(() => {
    if (currentStep.value === 4 && form.package_id) {
        return props.packages.find(p => p.id === form.package_id) || recommendedPackage.value;
    }
    return recommendedPackage.value;
});

const displayPackageName = computed(() => {
    return displayPackage.value ? `${displayPackage.value.name} Plan` : 'Starter Plan';
});

const displayFeatures = computed(() => {
    if (!displayPackage.value || !displayPackage.value.features) return [];
    return displayPackage.value.features
        .filter(f => f.is_enabled || f.limit !== null)
        .slice(0, 6);
});

const validatingCode = ref(false);
const validCode = ref(!!props.referral_code);
const invalidCodeMsg = ref('');
const referralResellerName = ref('');

const validatingCoupon = ref(false);
const validCoupon = ref(false);
const couponMessage = ref('');
const invalidCouponMsg = ref('');

let timeout = null;
watch(() => form.referral_code, (val) => {
    if (props.referral_code) return; // Locked from link
    invalidCodeMsg.value = '';
    validCode.value = false;
    referralResellerName.value = '';
    
    if (!val) return;

    if (val !== val.toUpperCase()) {
        form.referral_code = val.toUpperCase();
        return;
    }

    clearTimeout(timeout);
    timeout = setTimeout(async () => {
        validatingCode.value = true;
        try {
            const res = await axios.get('/api/promo-code/validate', { params: { code: val, type: 'referral' } });
            if (res.data.valid) {
                validCode.value = true;
                if (res.data.reseller_name) {
                    referralResellerName.value = res.data.reseller_name;
                }
            } else {
                invalidCodeMsg.value = res.data.message || 'Invalid code';
            }
        } catch (e) {
            invalidCodeMsg.value = 'Invalid code';
        }
        validatingCode.value = false;
    }, 500);
});

let couponTimeout = null;
watch(() => form.coupon_code, (val) => {
    invalidCouponMsg.value = '';
    validCoupon.value = false;
    couponMessage.value = '';
    if (!val) return;

    if (val !== val.toUpperCase()) {
        form.coupon_code = val.toUpperCase();
        return;
    }

    clearTimeout(couponTimeout);
    couponTimeout = setTimeout(async () => {
        validatingCoupon.value = true;
        invalidCouponMsg.value = '';
        validCoupon.value = false;
        
        try {
            const res = await axios.get('/api/promo-code/validate', { params: { code: val, type: 'coupon', package_id: form.package_id } });
            if (res.data.valid) {
                validCoupon.value = true;
                couponMessage.value = res.data.message;
            } else {
                invalidCouponMsg.value = res.data.message || 'Invalid coupon';
            }
        } catch (e) {
            invalidCouponMsg.value = 'Invalid coupon';
        }
        validatingCoupon.value = false;
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
        preserveScroll: true,
        onError: (errors) => {
            if (errors.company_name || errors.company_type || errors.branch_count) {
                currentStep.value = 1;
            } else if (errors.company_country || errors.company_currency || errors.company_timezone || errors.company_address || errors.company_phone) {
                currentStep.value = 2;
            } else if (errors.owner_name || errors.owner_email || errors.owner_phone || errors.password) {
                currentStep.value = 3;
                if (errors.password) {
                    form.reset('password', 'password_confirmation');
                }
            } else if (errors.package_id || errors.billing_cycle || errors.referral_code || errors.coupon_code) {
                currentStep.value = 4;
            }
            
            // Wait for Vue to render the step change, then focus the first error
            setTimeout(() => {
                const firstErrorElement = document.querySelector('.text-red-500');
                if (firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Try to focus the previous input sibling if possible
                    const inputElement = firstErrorElement.previousElementSibling;
                    if (inputElement && inputElement.focus) {
                        inputElement.focus();
                    }
                }
            }, 100);
        },
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
        }
    });
};
</script>

<template>
    <Head title="Register your Pharmacy" />

    <div class="min-h-screen bg-[#F8FAFC] font-sans overflow-x-hidden relative">
        <div class="flex flex-row w-[200vw] lg:w-full transition-transform duration-500 ease-in-out min-h-screen" :class="showMobileForm ? '-translate-x-[100vw] lg:translate-x-0' : 'translate-x-0'">
            
            <!-- LEFT PANEL (Brand & Trust) -->
            <div class="w-[100vw] lg:w-[30%] shrink-0 bg-[#024329] relative flex flex-col overflow-hidden text-white p-8 xl:p-12 min-h-screen lg:min-h-0">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgyNTUsIDI1NSwgMjU1LCAwLjA1KSIvPjwvc3ZnPg==')] opacity-50 z-0"></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#00b67a] opacity-20 rounded-full blur-[100px] z-0"></div>
            <div class="absolute bottom-40 -left-40 w-96 h-96 bg-[#00b67a] opacity-20 rounded-full blur-[100px] z-0"></div>
            
            <!-- Logo -->
            <Link :href="route('home')" class="relative z-10 flex items-center gap-3 mb-10 hover:opacity-90 transition-opacity w-max">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#00b67a] font-black text-2xl shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight leading-none text-white">SaaSMedi</h1>
                    <p class="text-[11px] font-medium text-white opacity-80 mt-1">Smart Pharmacy. Stronger Business.</p>
                </div>
            </Link>

            <!-- Content Area (Text + Hero) -->
            <div class="relative z-10 flex flex-col">
                <!-- Floating 3D Icons -->
                <div class="absolute right-0 top-0 w-20 h-20 opacity-90 animate-pulse" style="animation-duration: 4s;">
                    <img src="/images/shield_3d.png" class="w-full h-full object-contain drop-shadow-xl" alt="Shield Icon">
                </div>
                <div class="absolute right-14 top-24 w-12 h-12 opacity-80 animate-bounce" style="animation-duration: 5s;">
                    <img src="/images/pill_3d.png" class="w-full h-full object-contain drop-shadow-xl" alt="Pill Icon" style="transform: rotate(-15deg);">
                </div>
                <div class="absolute right-4 top-48 w-24 h-24 opacity-90 animate-pulse" style="animation-duration: 6s;">
                    <img src="/images/bottle_3d.png" class="w-full h-full object-contain drop-shadow-xl" alt="Bottle Icon">
                </div>
                <div class="absolute right-28 top-[22rem] w-12 h-12 opacity-80 animate-bounce" style="animation-duration: 4.5s;">
                    <img src="/images/pill_3d.png" class="w-full h-full object-contain drop-shadow-xl" alt="Pill Icon" style="transform: rotate(45deg);">
                </div>
                <h2 class="text-5xl font-bold leading-[1.15] mb-5 max-w-md">
                    Join 500+<br>pharmacies<br>growing with<br><span class="text-[#00b67a]">SaaSMedi.</span>
                </h2>
                
                <p class="text-[15px] font-medium opacity-90 mb-8 leading-relaxed max-w-sm">
                    All-in-one cloud based platform<br>to manage and grow your<br>pharmacy business.
                </p>

                <!-- Checkmarks -->
                <ul class="space-y-3.5 mb-8">
                    <li class="flex items-center gap-3 font-medium text-sm opacity-95">
                        <div class="w-5 h-5 rounded-full border border-[#00b67a] flex items-center justify-center bg-transparent">
                            <svg class="w-3 h-3 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        14-day free trial
                    </li>
                    <li class="flex items-center gap-3 font-medium text-sm opacity-95">
                        <div class="w-5 h-5 rounded-full border border-[#00b67a] flex items-center justify-center bg-transparent">
                            <svg class="w-3 h-3 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        No credit card required
                    </li>
                    <li class="flex items-center gap-3 font-medium text-sm opacity-95">
                        <div class="w-5 h-5 rounded-full border border-[#00b67a] flex items-center justify-center bg-transparent">
                            <svg class="w-3 h-3 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Cancel anytime
                    </li>
                    <li class="flex items-center gap-3 font-medium text-sm opacity-95">
                        <div class="w-5 h-5 rounded-full border border-[#00b67a] flex items-center justify-center bg-transparent">
                            <svg class="w-3 h-3 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Setup in 5 minutes
                    </li>
                    <li class="flex items-center gap-3 font-medium text-sm opacity-95">
                        <div class="w-5 h-5 rounded-full border border-[#00b67a] flex items-center justify-center bg-transparent">
                            <svg class="w-3 h-3 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        24/7 expert support
                    </li>
                </ul>

                <!-- Main Hero Image positioned relative in the flow but overlapping -->
                <div class="-mt-16 -mr-12 z-10 relative h-52 md:h-60 lg:h-72 flex justify-end">
                    <img src="/images/pharma_hero_3d.png" alt="Pharmacy Dashboard and 3D Assets" class="absolute bottom-0 right-0 w-[120%] max-w-[650px] h-auto object-contain drop-shadow-2xl" />
                </div>
            </div>

            <!-- Trust Bar Bottom Box -->
            <div class="relative z-10 mt-6 bg-[#013520] border border-[#005f3e] rounded-2xl p-4 xl:p-5 shadow-xl">
                <!-- Stats row — wraps on mobile -->
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <!-- Faces Box -->
                    <div class="flex flex-col items-center">
                        <div class="flex -space-x-2.5 mb-1.5 relative">
                            <img src="https://ui-avatars.com/api/?name=U1&background=random" class="w-7 h-7 xl:w-8 xl:h-8 rounded-full border-2 border-[#013520]" />
                            <img src="https://ui-avatars.com/api/?name=U2&background=random" class="w-7 h-7 xl:w-8 xl:h-8 rounded-full border-2 border-[#013520]" />
                            <img src="https://ui-avatars.com/api/?name=U3&background=random" class="w-7 h-7 xl:w-8 xl:h-8 rounded-full border-2 border-[#013520]" />
                            <div class="w-7 h-7 xl:w-8 xl:h-8 rounded-full border-2 border-[#013520] bg-[#00b67a] text-white flex items-center justify-center text-[8px] xl:text-[9px] font-bold z-10 relative">500+</div>
                        </div>
                        <div class="font-bold text-xs xl:text-sm text-[#00b67a]">500+</div>
                        <div class="text-[8px] xl:text-[9px] text-white opacity-90 font-medium">Pharmacies Trust Us</div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="flex gap-4 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-amber-400 mb-1">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <div class="font-bold text-sm">4.9/5</div>
                            <div class="text-[8px] xl:text-[9px] text-white opacity-80 font-medium mt-0.5">Rating</div>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-[#00b67a] mb-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.6-6.3-4.6-6.3 4.6 2.3-7.6-6-4.6h7.6z"/></svg>
                            </div>
                            <div class="font-bold text-sm">50K+</div>
                            <div class="text-[8px] xl:text-[9px] text-white opacity-80 font-medium mt-0.5">Orders/day</div>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-[#00b67a] mb-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="font-bold text-sm">99.9%</div>
                            <div class="text-[8px] xl:text-[9px] text-white opacity-80 font-medium mt-0.5">Uptime</div>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-[#00b67a] mb-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div class="font-bold text-sm">24/7</div>
                            <div class="text-[8px] xl:text-[9px] text-white opacity-80 font-medium mt-0.5">Support</div>
                        </div>
                    </div>
                </div>
            </div><!-- end trust bar -->

            <!-- Mobile Start Button (outside trust bar, full width below it) -->
            <div class="lg:hidden mt-4 relative z-20">
                <button @click="showMobileForm = true" class="w-full py-4 bg-[#00b67a] hover:bg-[#00a06b] text-white rounded-xl font-bold text-lg shadow-[0_0_20px_rgba(0,182,122,0.3)] transition-all flex items-center justify-center gap-2">
                    Start Registration
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
            </div><!-- end left panel -->


            <!-- RIGHT PANEL (Wizard) -->
            <div class="w-[100vw] lg:w-[70%] shrink-0 bg-white flex flex-col relative min-h-screen">
                
                <!-- Mobile Header -->
                <div class="lg:hidden p-6 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-20">
                    <button @click="showMobileForm = false" class="text-slate-500 hover:text-slate-800 p-2 -ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-[#006E48] rounded-lg flex items-center justify-center text-white font-black text-xl shadow-md">+</div>
                        <span class="font-black text-xl text-slate-800 tracking-tight">SaaSMedi</span>
                    </div>
                    <Link :href="route('login')" class="text-sm font-bold text-slate-500 hover:text-slate-800">Log in</Link>
                </div>



            <!-- Progress Bar Tracker -->
            <div class="max-w-4xl mx-auto w-full px-6 xl:px-12 pt-6 pb-4">
                <!-- Step Labels Row -->
                <div class="relative flex items-start justify-between">
                    
                    <!-- Background track line -->
                    <div class="absolute left-0 right-0 top-6 h-1 bg-slate-100 rounded-full z-0" style="left:calc(12.5%); right:calc(12.5%)"></div>
                    <!-- Animated progress line -->
                    <div class="absolute top-6 h-1 bg-gradient-to-r from-[#00b67a] to-[#009e6a] rounded-full z-0 transition-all duration-700 ease-out"
                        :style="`left:calc(12.5%); width: calc(${(currentStep-1)/3 * 75}%)`"></div>

                    <!-- Each Step -->
                    <div v-for="step in 4" :key="step"
                        class="relative z-10 flex flex-col items-center gap-1.5 cursor-pointer w-1/4"
                        @click="currentStep = step">

                        <!-- Circle -->
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-base transition-all duration-300 shadow-sm"
                            :class="step < currentStep
                                ? 'bg-[#00b67a] text-white ring-2 ring-[#00b67a]/20'
                                : step === currentStep
                                    ? 'bg-[#024329] text-white ring-2 ring-[#024329]/20 scale-110 shadow-md'
                                    : 'bg-white border-2 border-slate-200 text-slate-400'">
                            <svg v-if="step < currentStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            <template v-else>
                                <span v-if="step === 1" class="text-sm">🏪</span>
                                <span v-else-if="step === 2" class="text-sm">⚙️</span>
                                <span v-else-if="step === 3" class="text-sm">👤</span>
                                <span v-else class="text-sm">💳</span>
                            </template>
                        </div>

                        <!-- Label -->
                        <div class="text-center">
                            <div class="text-[11px] font-black tracking-wide transition-colors duration-300"
                                :class="step <= currentStep ? 'text-[#024329]' : 'text-slate-400'">
                                {{ step === 1 ? 'Business' : step === 2 ? 'Setup' : step === 3 ? 'Admin' : 'Plan' }}
                            </div>
                            <div class="text-[9px] font-medium text-slate-400 hidden sm:block leading-tight">
                                {{ step === 1 ? 'Profile' : step === 2 ? 'Location' : step === 3 ? 'Account' : 'Choose' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="max-w-5xl mx-auto w-full px-6 xl:px-12 pb-8 flex flex-col xl:flex-row gap-6 xl:gap-10">
                
                <!-- Left Form Side -->
                <div class="flex-1 min-w-0">
                    <form @submit.prevent="submit" class="flex flex-col">
                        
                        <div>
                            <!-- STEP 1: Business Profile -->
                            <div v-if="currentStep === 1" class="space-y-8 animate-fade-in">
                                <div>
                                    <h2 class="text-2xl font-black text-slate-800">Business Profile</h2>
                                    <p class="text-sm font-medium text-slate-500 mt-1">Let's start with your pharmacy's basic details.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Business Name <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <input v-model="form.company_name" type="text" class="pl-11 w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium text-slate-800 transition-shadow" placeholder="Example Pharmacy Ltd." required>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-400 mt-2">This will be your business name on invoices and reports.</p>
                                    <p v-if="form.errors.company_name" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-3">Business Type <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                            :class="form.company_type === 'Retail Pharmacy' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.company_type" value="Retail Pharmacy" class="sr-only">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-xl">🏪</div>
                                                <div v-if="form.company_type === 'Retail Pharmacy'" class="w-5 h-5 rounded-full bg-[#00b67a] text-white flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            </div>
                                            <div class="font-bold text-sm text-slate-800">Retail Pharmacy</div>
                                            <div class="text-[11px] font-medium text-slate-500 mt-1 leading-relaxed">Single branch pharmacy directly serving consumers.</div>
                                        </label>

                                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                            :class="form.company_type === 'Chain Pharmacy' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.company_type" value="Chain Pharmacy" class="sr-only">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-xl">🏢</div>
                                                <div v-if="form.company_type === 'Chain Pharmacy'" class="w-5 h-5 rounded-full bg-[#00b67a] text-white flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            </div>
                                            <div class="font-bold text-sm text-slate-800">Chain Pharmacy</div>
                                            <div class="text-[11px] font-medium text-slate-500 mt-1 leading-relaxed">Multiple branches managed centrally under one brand.</div>
                                        </label>

                                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                            :class="form.company_type === 'Hospital Pharmacy' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.company_type" value="Hospital Pharmacy" class="sr-only">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center text-xl">🏥</div>
                                                <div v-if="form.company_type === 'Hospital Pharmacy'" class="w-5 h-5 rounded-full bg-[#00b67a] text-white flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            </div>
                                            <div class="font-bold text-sm text-slate-800">Hospital Pharmacy</div>
                                            <div class="text-[11px] font-medium text-slate-500 mt-1 leading-relaxed">In-house pharmacy for a hospital or clinic.</div>
                                        </label>

                                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                            :class="form.company_type === 'Distributor' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.company_type" value="Distributor" class="sr-only">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-xl">🚚</div>
                                                <div v-if="form.company_type === 'Distributor'" class="w-5 h-5 rounded-full bg-[#00b67a] text-white flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            </div>
                                            <div class="font-bold text-sm text-slate-800">Distributor</div>
                                            <div class="text-[11px] font-medium text-slate-500 mt-1 leading-relaxed">Medicine distributor or wholesaler managing bulk stock.</div>
                                        </label>
                                    </div>
                                    <p v-if="form.errors.company_type" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_type }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-3">Number of Branches <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer text-center transition-all duration-200"
                                            :class="form.branch_count === '1' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.branch_count" value="1" class="sr-only">
                                            <div class="text-xl mb-1">🏪</div>
                                            <div class="font-black text-sm text-slate-800">1</div>
                                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-0.5">Branch</div>
                                        </label>
                                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer text-center transition-all duration-200"
                                            :class="form.branch_count === '2-5' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.branch_count" value="2-5" class="sr-only">
                                            <div class="text-xl mb-1">🏪🏪</div>
                                            <div class="font-black text-sm text-slate-800">2-5</div>
                                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-0.5">Branches</div>
                                        </label>
                                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer text-center transition-all duration-200"
                                            :class="form.branch_count === '6-20' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.branch_count" value="6-20" class="sr-only">
                                            <div class="text-xl mb-1">🏢🏢</div>
                                            <div class="font-black text-sm text-slate-800">6-20</div>
                                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-0.5">Branches</div>
                                        </label>
                                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer text-center transition-all duration-200"
                                            :class="form.branch_count === '20+' ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                            <input type="radio" v-model="form.branch_count" value="20+" class="sr-only">
                                            <div class="text-xl mb-1">🏙️</div>
                                            <div class="font-black text-sm text-slate-800">20+</div>
                                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-0.5">Branches</div>
                                        </label>
                                    </div>
                                    <p v-if="form.errors.branch_count" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.branch_count }}</p>
                                </div>
                            </div>
                            <!-- END STEP 1 -->

                            <!-- STEP 2: Setup -->
                            <div v-else-if="currentStep === 2" class="space-y-6 animate-fade-in">
                                <div>
                                    <h2 class="text-2xl font-black text-slate-800">Localization</h2>
                                    <p class="text-sm font-medium text-slate-500 mt-1">Configure your region settings.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Country <span class="text-red-500">*</span></label>
                                        <input v-model="form.company_country" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow">
                                        <p v-if="form.errors.company_country" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_country }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Currency <span class="text-red-500">*</span></label>
                                        <input v-model="form.company_currency" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow">
                                        <p v-if="form.errors.company_currency" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_currency }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Timezone <span class="text-red-500">*</span></label>
                                    <input v-model="form.company_timezone" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow">
                                    <p v-if="form.errors.company_timezone" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_timezone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Business Address <span class="text-red-500">*</span></label>
                                    <textarea v-model="form.company_address" rows="2" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow"></textarea>
                                    <p v-if="form.errors.company_address" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Business Phone <span class="text-red-500">*</span></label>
                                    <input v-model="form.company_phone" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow">
                                    <p v-if="form.errors.company_phone" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.company_phone }}</p>
                                </div>
                            </div>

                            <!-- STEP 3: Admin -->
                            <div v-else-if="currentStep === 3" class="space-y-6 animate-fade-in">
                                <div>
                                    <h2 class="text-2xl font-black text-slate-800">Admin Account</h2>
                                    <p class="text-sm font-medium text-slate-500 mt-1">Create the owner account for this system.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Full Name <span class="text-red-500">*</span></label>
                                    <input v-model="form.owner_name" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow" required>
                                    <p v-if="form.errors.owner_name" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.owner_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input v-model="form.owner_email" type="email" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow" required>
                                    <p v-if="form.errors.owner_email" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.owner_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                                    <input v-model="form.owner_phone" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow" required>
                                    <p v-if="form.errors.owner_phone" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.owner_phone }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Password <span class="text-red-500">*</span></label>
                                        <input v-model="form.password" type="password" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow" required>
                                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-medium">{{ form.errors.password }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                        <input v-model="form.password_confirmation" type="password" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-medium transition-shadow" required>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 4: Plan Checkout -->
                            <div v-else-if="currentStep === 4" class="space-y-5 animate-fade-in">
                                <div>
                                    <h2 class="text-2xl font-black text-slate-800">Choose Your Plan</h2>
                                    <p class="text-sm font-medium text-slate-500 mt-1">Select the plan that fits your pharmacy. Start free for 14 days.</p>
                                </div>

                                <!-- Billing Cycle Toggle -->
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button"
                                        @click="form.billing_cycle = 'monthly'"
                                        class="px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200"
                                        :class="form.billing_cycle === 'monthly' ? 'bg-[#024329] text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                                        Monthly
                                    </button>
                                    <button type="button"
                                        @click="form.billing_cycle = 'yearly'"
                                        class="px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2"
                                        :class="form.billing_cycle === 'yearly' ? 'bg-[#024329] text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                                        Yearly
                                        <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full"
                                            :class="form.billing_cycle === 'yearly' ? 'bg-[#00b67a] text-white' : 'bg-emerald-100 text-emerald-700'">
                                            Save ~17%
                                        </span>
                                    </button>
                                </div>

                                <!-- Package Cards -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label v-for="pkg in packages" :key="pkg.id"
                                        class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                        :class="form.package_id == pkg.id
                                            ? 'border-[#00b67a] bg-[#F3FBF8] shadow-sm'
                                            : 'border-slate-100 hover:border-slate-200 bg-white'">
                                        <input type="radio" v-model="form.package_id" :value="pkg.id" class="sr-only">

                                        <!-- Recommended Badge -->
                                        <div v-if="pkg.id === recommendedPackage?.id"
                                            class="absolute -top-2.5 left-4 px-2.5 py-0.5 bg-[#00b67a] text-white text-[9px] font-black uppercase tracking-wider rounded-full">
                                            ⭐ Recommended
                                        </div>

                                        <!-- Selected check -->
                                        <div v-if="form.package_id == pkg.id"
                                            class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#00b67a] text-white flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>

                                        <!-- Plan name & price -->
                                        <div class="mb-3">
                                            <div class="font-black text-slate-800 text-base">{{ pkg.name }}</div>
                                            <div class="flex items-baseline gap-1 mt-1">
                                                <template v-if="form.billing_cycle === 'monthly'">
                                                    <span v-if="parseFloat(pkg.monthly_price) === 0" class="text-2xl font-black text-[#00b67a]">Free</span>
                                                    <template v-else>
                                                        <span class="text-2xl font-black text-slate-800">৳{{ Number(parseFloat(pkg.monthly_price)).toLocaleString() }}</span>
                                                        <span class="text-xs font-medium text-slate-400">/mo</span>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <span v-if="parseFloat(pkg.yearly_price) === 0" class="text-2xl font-black text-[#00b67a]">Free</span>
                                                    <template v-else>
                                                        <span class="text-2xl font-black text-slate-800">৳{{ Number(parseFloat(pkg.yearly_price)).toLocaleString() }}</span>
                                                        <span class="text-xs font-medium text-slate-400">/yr</span>
                                                    </template>
                                                </template>
                                            </div>
                                            <div v-if="form.billing_cycle === 'yearly' && parseFloat(pkg.yearly_price) > 0" class="text-[10px] text-emerald-600 font-bold mt-0.5">
                                                ৳{{ Math.round(parseFloat(pkg.yearly_price) / 12).toLocaleString() }}/mo billed annually
                                            </div>
                                        </div>

                                        <!-- Top features -->
                                        <ul class="space-y-1.5">
                                            <li v-for="feature in (pkg.features || []).filter(f => f.is_enabled || f.limit !== null).slice(0, 4)"
                                                :key="feature.id"
                                                class="flex items-center gap-1.5 text-[11px] font-medium text-slate-600">
                                                <svg class="w-3 h-3 text-[#00b67a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span class="capitalize">
                                                    <template v-if="feature.limit !== null">{{ feature.limit }} </template>
                                                    {{ feature.feature_code.replace(/_/g, ' ') }}
                                                </span>
                                            </li>
                                        </ul>
                                    </label>
                                </div>

                                <!-- Referral Code & Promo Code -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Partner Referral Code -->
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Partner Referral Code</label>
                                        <div class="relative">
                                            <input v-model="form.referral_code" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-bold uppercase transition-shadow pr-10" placeholder="Optional">
                                            
                                            <!-- Validation Status -->
                                            <div class="mt-1 flex items-center min-h-[20px]">
                                                <span v-if="validatingCode" class="text-xs font-bold text-slate-500 flex items-center gap-1"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Checking...</span>
                                                <span v-else-if="validCode" class="text-xs font-bold text-emerald-600 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ referralResellerName ? 'Partner: ' + referralResellerName : 'Applied' }}</span>
                                                <span v-else-if="invalidCodeMsg" class="text-xs font-bold text-red-500">{{ invalidCodeMsg }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Promo / Discount Code -->
                                    <div>
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Promo / Coupon Code</label>
                                        <div class="relative">
                                            <input v-model="form.coupon_code" type="text" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#00b67a] focus:ring-[#00b67a]/20 py-3 text-sm font-bold uppercase transition-shadow pr-10" placeholder="Optional">
                                            
                                            <!-- Validation Status -->
                                            <div class="mt-1 flex items-center min-h-[20px]">
                                                <span v-if="validatingCoupon" class="text-xs font-bold text-slate-500 flex items-center gap-1"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Checking...</span>
                                                <span v-else-if="validCoupon" class="text-xs font-bold text-emerald-600 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ couponMessage }}</span>
                                                <span v-else-if="invalidCouponMsg" class="text-xs font-bold text-red-500">{{ invalidCouponMsg }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trial note -->
                                <div class="p-4 bg-[#F3FBF8] border border-[#00b67a]/30 rounded-xl flex items-center gap-3">
                                    <svg class="w-5 h-5 text-[#00b67a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs font-medium text-slate-600">Your <span class="font-bold text-[#00b67a]">14-day free trial</span> starts immediately. No credit card required. Cancel anytime.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-6 pt-5 flex justify-between items-center bg-white border-t border-slate-100">
                            <button type="button" @click="prevStep" class="flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm" :class="currentStep === 1 ? 'invisible' : ''">
                                ← Back
                            </button>
                            
                            <div class="flex items-center gap-4">
                                <button type="button" class="text-sm font-bold text-slate-500 hover:text-slate-800 hidden sm:block">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                        Save & Exit
                                    </span>
                                </button>
                                
                                <button v-if="currentStep < totalSteps" type="button" @click="nextStep" class="group bg-[#00b67a] hover:bg-[#009e6a] text-white px-8 py-3.5 rounded-xl text-sm font-black shadow-lg shadow-[#00b67a]/20 transition-all flex flex-col items-center">
                                    <div class="flex items-center gap-2">
                                        Continue Setup
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                    <div class="text-[10px] font-medium text-emerald-100 opacity-90 mt-0.5 font-normal tracking-wide">Estimated time: 3 minutes</div>
                                </button>
                                
                                <button v-if="currentStep === totalSteps" type="submit" :disabled="form.processing" class="group bg-[#00b67a] hover:bg-[#009e6a] text-white px-8 py-3.5 rounded-xl text-sm font-black shadow-lg shadow-[#00b67a]/20 transition-all flex flex-col items-center">
                                    <div class="flex items-center gap-2">
                                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Complete Registration
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Login Link -->
                        <div class="mt-6 text-center">
                            <span class="text-sm font-medium text-slate-500">Already have an account? </span>
                            <Link :href="route('login')" class="text-sm font-bold text-[#006E48] hover:underline">Log in here</Link>
                        </div>

                    </form>
                </div>

                <!-- Right Summary Side (Hidden on Mobile) -->
                <div class="hidden xl:block w-72 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sticky top-24">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div class="w-8 h-8 rounded-lg bg-[#F3FBF8] flex items-center justify-center text-[#00b67a]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="font-black text-slate-800 text-sm tracking-wide uppercase">Business Summary</h3>
                        </div>

                        <div class="space-y-4 mb-6">
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 flex-shrink-0 text-slate-400 mt-0.5"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Business Type</div>
                                    <div class="text-sm font-bold text-slate-800">{{ form.company_type || 'Not selected' }}</div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 flex-shrink-0 text-slate-400 mt-0.5"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Branches</div>
                                    <div class="text-sm font-bold text-slate-800">{{ form.branch_count }} {{ form.branch_count === '1' ? 'Branch' : 'Branches' }}</div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 flex-shrink-0 text-amber-500 mt-0.5"><svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex justify-between items-center">
                                        {{ currentStep === 4 ? 'Selected Plan' : 'Recommended Plan' }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-bold text-slate-800">{{ displayPackageName }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-5 border-t border-slate-100">
                            <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-3">You'll Get</h4>
                            <ul class="space-y-2.5">
                                <li v-for="feature in displayFeatures" :key="feature.id" class="flex items-center gap-2 text-xs font-bold text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-[#00b67a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span class="capitalize">
                                        <template v-if="feature.limit !== null">{{ feature.limit }} </template>
                                        {{ feature.feature_code.replace(/_/g, ' ') }}
                                    </span>
                                </li>
                                <li v-if="displayPackage?.features?.length > 6" class="flex items-center gap-2 text-[11px] font-bold text-slate-400 pl-5 pt-1">
                                    And much more...
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mt-6 p-4 bg-[#F8FAFC] rounded-xl border border-slate-100 flex gap-3">
                            <svg class="w-5 h-5 text-[#00b67a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <div>
                                <h5 class="text-xs font-bold text-slate-800">Secure & Reliable</h5>
                                <p class="text-[10px] font-medium text-slate-500 mt-1 leading-relaxed">Your data is 100% safe with enterprise-grade security.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!-- end main content area -->
            </div><!-- end right panel -->
        </div><!-- end sliding track -->
    </div><!-- end outer wrapper -->
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px) scale(0.99);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>
