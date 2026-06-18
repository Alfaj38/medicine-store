<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    plans: Array
});

const form = useForm({
    company_name: '',
    company_phone: '',
    company_address: '',
    owner_name: '',
    owner_email: '',
    owner_phone: '',
    password: '',
    password_confirmation: '',
    plan_id: props.plans[0]?.id || '',
    billing_cycle: 'monthly',
});

const submit = () => {
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register - MediSaaS" />
    
    <div class="min-h-screen bg-[#e8f7f2] flex items-center justify-center font-sans p-4 py-12" 
         style="background-image: radial-gradient(#d1ebe1 1px, transparent 1px); background-size: 20px 20px;">
        
        <div class="w-full max-w-[800px] bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col min-h-[600px] p-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Create your</h1>
                <h2 class="text-3xl font-bold text-[#344b8b] mt-1">MediSaaS Account</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                
                <!-- Company Details -->
                <div class="space-y-4">
                    <h3 class="font-bold border-b pb-2 text-slate-700">1. Company Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Company Name</label>
                            <input v-model="form.company_name" type="text" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.company_name" class="text-red-500 text-xs mt-1">{{ form.errors.company_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Company Phone</label>
                            <input v-model="form.company_phone" type="text" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.company_phone" class="text-red-500 text-xs mt-1">{{ form.errors.company_phone }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Company Address</label>
                            <input v-model="form.company_address" type="text" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.company_address" class="text-red-500 text-xs mt-1">{{ form.errors.company_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Owner Details -->
                <div class="space-y-4">
                    <h3 class="font-bold border-b pb-2 text-slate-700">2. Administrator Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name</label>
                            <input v-model="form.owner_name" type="text" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.owner_name" class="text-red-500 text-xs mt-1">{{ form.errors.owner_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                            <input v-model="form.owner_email" type="email" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.owner_email" class="text-red-500 text-xs mt-1">{{ form.errors.owner_email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number</label>
                            <input v-model="form.owner_phone" type="text" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.owner_phone" class="text-red-500 text-xs mt-1">{{ form.errors.owner_phone }}</p>
                        </div>
                        <div class="hidden md:block"></div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                            <input v-model="form.password" type="password" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" required class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3 focus:border-emerald-500 focus:outline-none transition-colors text-sm" />
                        </div>
                    </div>
                </div>

                <!-- Subscription Selection -->
                <div class="space-y-4">
                    <h3 class="font-bold border-b pb-2 text-slate-700">3. Select Plan</h3>
                    
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.billing_cycle" value="monthly" class="text-[#344b8b] focus:ring-[#344b8b]" />
                            <span class="text-sm font-semibold">Monthly Billing</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.billing_cycle" value="yearly" class="text-[#344b8b] focus:ring-[#344b8b]" />
                            <span class="text-sm font-semibold">Yearly Billing (Save 10%)</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <label v-for="plan in plans" :key="plan.id" class="cursor-pointer">
                            <input type="radio" v-model="form.plan_id" :value="plan.id" class="peer sr-only" />
                            <div class="rounded-xl border-2 border-slate-200 p-4 hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all text-center h-full flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ plan.name }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">{{ plan.description }}</p>
                                </div>
                                <div class="mt-4">
                                    <span class="text-2xl font-bold text-emerald-600">
                                        {{ plan.prices.find(p => p.billing_cycle === form.billing_cycle)?.total_price || 0 }}
                                    </span>
                                    <span class="text-xs text-slate-500"> BDT</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p v-if="form.errors.plan_id" class="text-red-500 text-xs mt-1">{{ form.errors.plan_id }}</p>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <a :href="route('login')" class="text-sm font-semibold text-[#344b8b] hover:underline">Back to Login</a>
                    
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-8 bg-[#344b8b] text-white rounded-full py-3 text-sm font-bold flex items-center justify-center gap-2 hover:bg-[#283b70] transition-colors disabled:opacity-50 shadow-lg"
                    >
                        <span v-if="form.processing">Registering...</span>
                        <template v-else>
                            Create Account
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
