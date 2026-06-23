<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref } from 'vue';

const form = useForm({
    name: '',
    business_name: '',
    email: '',
    phone: '',
    nid_or_business_id: '',
    address: '',
    experience: '',
    password: '',
    password_confirmation: '',
    bank_info: {
        method: 'bank',
        account_name: '',
        account_number: '',
        bank_name: ''
    }
});

const submit = () => {
    form.post(route('partner.apply'));
};
</script>

<template>
    <Head title="Partner Program - SaaSMedi" />
    <PublicLayout>
        <!-- Hero Section -->
        <section class="relative bg-slate-900 pt-32 pb-20 overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CgkJPGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiMzMzQxNTUiIGZpbGwtb3BhY2l0eT0iMC40Ii8+Cgk8L3N2Zz4=')] opacity-30"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-6">
                    Earn <span class="text-emerald-400">Recurring Income</span><br />
                    By Referring Pharmacies
                </h1>
                <p class="text-xl text-slate-300 max-w-2xl mx-auto mb-10">
                    Join the SaaSMedi Partner Program and earn a 10% lifetime commission for every pharmacy you refer that becomes a subscriber.
                </p>
                <a href="#apply" class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-lg transition-colors shadow-lg shadow-emerald-500/30">
                    Apply Now
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </a>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">10% Lifetime Commission</h3>
                        <p class="text-slate-600">Earn 10% of the subscription fee every single time your referred company pays, for life.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Dashboard</h3>
                        <p class="text-slate-600">Track your clicks, referrals, and earnings in real-time through your dedicated partner portal.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Fast Payouts</h3>
                        <p class="text-slate-600">Withdraw your earnings directly to your Bank, bKash, or Nagad account quickly and securely.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Application Form -->
        <section id="apply" class="py-20 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 border border-slate-100">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-black text-slate-900">Partner Application</h2>
                        <p class="text-slate-500 mt-2">Fill out the form below to apply. Approvals take 1-2 business days.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                <input v-model="form.name" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Business / Agency Name</label>
                                <input v-model="form.business_name" type="text" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                <input v-model="form.email" type="email" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                                <input v-model="form.phone" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                                <input v-model="form.password" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                                <input v-model="form.password_confirmation" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">NID or Trade License No <span class="text-red-500">*</span></label>
                            <input v-model="form.nid_or_business_id" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Full Address <span class="text-red-500">*</span></label>
                            <textarea v-model="form.address" rows="3" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                        </div>

                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Payout Information</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Preferred Payout Method <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="form.bank_info.method === 'bank' ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500 text-emerald-700' : 'border-slate-200 text-slate-600'">
                                        <input type="radio" v-model="form.bank_info.method" value="bank" class="sr-only">
                                        <span class="text-sm font-bold">Bank</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="form.bank_info.method === 'bkash' ? 'border-[#e2136e] bg-pink-50 ring-1 ring-[#e2136e] text-[#e2136e]' : 'border-slate-200 text-slate-600'">
                                        <input type="radio" v-model="form.bank_info.method" value="bkash" class="sr-only">
                                        <span class="text-sm font-bold">bKash</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="form.bank_info.method === 'nagad' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500 text-orange-700' : 'border-slate-200 text-slate-600'">
                                        <input type="radio" v-model="form.bank_info.method" value="nagad" class="sr-only">
                                        <span class="text-sm font-bold">Nagad</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="form.bank_info.method === 'rocket' ? 'border-purple-500 bg-purple-50 ring-1 ring-purple-500 text-purple-700' : 'border-slate-200 text-slate-600'">
                                        <input type="radio" v-model="form.bank_info.method" value="rocket" class="sr-only">
                                        <span class="text-sm font-bold">Rocket</span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="form.bank_info.method === 'bank'" class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Bank Name & Branch <span class="text-red-500">*</span></label>
                                <input v-model="form.bank_info.bank_name" type="text" :required="form.bank_info.method === 'bank'" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Account Name <span class="text-red-500">*</span></label>
                                    <input v-model="form.bank_info.account_name" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Account Number <span class="text-red-500">*</span></label>
                                    <input v-model="form.bank_info.account_number" type="text" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Tell us about your experience referring software (Optional)</label>
                            <textarea v-model="form.experience" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                        </div>

                        <div class="pt-4 flex items-center justify-between">
                            <p class="text-sm text-slate-500">Already a partner? <Link :href="route('reseller.login')" class="font-bold text-emerald-600 hover:underline">Log in</Link></p>
                            
                            <button type="submit" :disabled="form.processing" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full font-bold shadow-lg shadow-emerald-500/30 transition-all disabled:opacity-70 flex items-center gap-2">
                                <svg v-if="form.processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
