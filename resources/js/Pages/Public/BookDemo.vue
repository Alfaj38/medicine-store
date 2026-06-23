<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company_name: '',
    branch_count: '1',
    current_software: [],
    pain_points: [],
    preferred_language: 'English',
    preferred_date: '',
    preferred_time: '',
    questions: '',
    referral_source: '',
});

const softwareOptions = ['Excel / Spreadsheet', 'Manual / Paper', 'Legacy Desktop Software', 'Other Software'];
const painPointOptions = ['Stock Expiry Tracking', 'Slow Billing', 'Multi-Branch Management', 'Inventory Control', 'Financial Reporting', 'Supplier Management'];
const timeSlots = ['9:00 AM', '11:00 AM', '2:00 PM', '4:00 PM'];
const referralOptions = ['Google Search', 'Facebook / Instagram', 'LinkedIn', 'Friend / Colleague', 'YouTube', 'Other'];

const toggleArray = (arr, val) => {
    const idx = arr.indexOf(val);
    if (idx === -1) arr.push(val);
    else arr.splice(idx, 1);
};

const submit = () => {
    form.post(route('demo.store'));
};

// Min date = tomorrow
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const minDate = tomorrow.toISOString().split('T')[0];
</script>

<template>
    <Head title="Book a Demo - SaaSMedi" />
    <PublicLayout>
        <div class="bg-gradient-to-br from-slate-50 to-emerald-50/30 min-h-screen py-16 px-4">
            <div class="max-w-6xl mx-auto">

                <!-- Header -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Free 30-Minute Live Demo
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 mb-3">See SaaSMedi in Action</h1>
                    <p class="text-slate-500 max-w-xl mx-auto">Book a personalized demo and see exactly how SaaSMedi solves your pharmacy's challenges.</p>
                </div>

                <div class="grid lg:grid-cols-5 gap-8 items-start">

                    <!-- Left: Value Pitch -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- What you'll see -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">What You'll See in 30 Mins</h3>
                            <ul class="space-y-4">
                                <li v-for="(item, i) in [
                                    { icon: '⚡', title: 'Live POS Billing', desc: 'Barcode scan, search & bill in under 5 seconds' },
                                    { icon: '📦', title: 'Batch & Expiry Tracking', desc: 'Never lose money to expired stock again' },
                                    { icon: '📊', title: 'Real-Time Reports', desc: 'Sales, profit, and stock insights at a glance' },
                                    { icon: '🌐', title: 'Multi-Branch Control', desc: 'Manage all branches from one dashboard' },
                                ]" :key="i" class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-lg flex-shrink-0">{{ item.icon }}</div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ item.title }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ item.desc }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Social proof -->
                        <div class="bg-emerald-600 rounded-2xl p-6 text-white">
                            <div class="text-3xl font-black mb-1">500+</div>
                            <div class="text-emerald-100 text-sm mb-4">Pharmacies already using SaaSMedi across Bangladesh</div>
                            <div class="flex -space-x-2">
                                <div v-for="i in 5" :key="i" class="w-8 h-8 rounded-full bg-white/20 border-2 border-emerald-600 flex items-center justify-center text-xs font-bold">
                                    {{ ['A','B','C','D','E'][i-1] }}
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white/10 border-2 border-emerald-600 flex items-center justify-center text-[10px] font-bold">+495</div>
                            </div>
                        </div>

                        <!-- Trust badges -->
                        <div class="grid grid-cols-2 gap-3">
                            <div v-for="badge in ['No Obligation', 'Free Session', 'Expert Guide', 'Custom Demo']" :key="badge"
                                class="bg-white border border-slate-200 rounded-xl p-3 text-center text-xs font-semibold text-slate-600 shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ badge }}
                            </div>
                        </div>
                    </div>

                    <!-- Right: Form -->
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-8">
                            <h2 class="text-xl font-bold text-slate-900 mb-6">Book Your Free Demo</h2>

                            <form @submit.prevent="submit" class="space-y-5">

                                <!-- Name + Email -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                        <input v-model="form.name" type="text" required placeholder="Your full name"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                        <input v-model="form.email" type="email" required placeholder="you@example.com"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                                    </div>
                                </div>

                                <!-- Phone + Company -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone / WhatsApp <span class="text-red-500">*</span></label>
                                        <input v-model="form.phone" type="text" required placeholder="+880 1..."
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Company Name <span class="text-red-500">*</span></label>
                                        <input v-model="form.company_name" type="text" required placeholder="Your pharmacy name"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                </div>

                                <!-- Branch Count -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Number of Branches <span class="text-red-500">*</span></label>
                                    <div class="flex flex-wrap gap-2">
                                        <button v-for="opt in ['1', '2-5', '6-20', '20+']" :key="opt" type="button"
                                            @click="form.branch_count = opt"
                                            :class="form.branch_count === opt ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-300 hover:border-emerald-400'"
                                            class="px-4 py-2 rounded-xl border text-sm font-semibold transition-all">
                                            {{ opt }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Currently Using -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Currently Using</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label v-for="opt in softwareOptions" :key="opt"
                                            class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                            :class="form.current_software.includes(opt) ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                                            <input type="checkbox" :value="opt" :checked="form.current_software.includes(opt)"
                                                @change="toggleArray(form.current_software, opt)" class="rounded text-emerald-600 w-4 h-4">
                                            <span class="text-xs font-medium text-slate-700">{{ opt }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Pain Points -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Your Biggest Pain Points</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label v-for="opt in painPointOptions" :key="opt"
                                            class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                            :class="form.pain_points.includes(opt) ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                                            <input type="checkbox" :value="opt" :checked="form.pain_points.includes(opt)"
                                                @change="toggleArray(form.pain_points, opt)" class="rounded text-emerald-600 w-4 h-4">
                                            <span class="text-xs font-medium text-slate-700">{{ opt }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Date + Time + Language -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Preferred Date <span class="text-red-500">*</span></label>
                                        <input v-model="form.preferred_date" type="date" required :min="minDate"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Preferred Time <span class="text-red-500">*</span></label>
                                        <select v-model="form.preferred_time" required
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                                            <option value="">Select time</option>
                                            <option v-for="t in timeSlots" :key="t" :value="t">{{ t }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Language</label>
                                        <select v-model="form.preferred_language"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                                            <option>English</option>
                                            <option>Bangla</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Referral Source -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">How did you hear about us?</label>
                                    <select v-model="form.referral_source"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white">
                                        <option value="">Select source</option>
                                        <option v-for="r in referralOptions" :key="r" :value="r">{{ r }}</option>
                                    </select>
                                </div>

                                <!-- Questions -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Any specific questions? <span class="text-slate-400 font-normal">(optional)</span></label>
                                    <textarea v-model="form.questions" rows="3" placeholder="Tell us what you'd like to focus on..."
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-none"></textarea>
                                </div>

                                <!-- Submit -->
                                <button type="submit" :disabled="form.processing"
                                    class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/25 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
                                    <svg v-if="form.processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ form.processing ? 'Booking...' : 'Book My Free Demo' }}
                                </button>

                                <p class="text-center text-xs text-slate-400">We'll send a confirmation email with your booking details.</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
