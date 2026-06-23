<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    plans: Array
});
</script>

<template>
    <Head title="Pricing - SaaSMedi" />
    <PublicLayout>
        <!-- Header -->
        <div class="bg-slate-50 pt-20 pb-24 border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">Simple, Transparent Pricing</h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Choose the perfect plan for your pharmacy. Upgrade, downgrade, or cancel at any time.</p>
                
                <!-- Toggle (Mockup representation) -->
                <div class="mt-10 inline-flex items-center gap-2 bg-slate-200/50 p-1 rounded-full border border-slate-200">
                    <button class="px-6 py-2 rounded-full text-sm font-bold bg-white text-slate-900 shadow-sm">Monthly</button>
                    <button class="px-6 py-2 rounded-full text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-2">
                        Yearly <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs">Save 20%</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 mb-24 relative z-10">
            <div class="grid md:grid-cols-3 gap-8 items-start">
                
                <div v-for="plan in plans" :key="plan.id" 
                    :class="['bg-white rounded-3xl p-8 transition-transform hover:-translate-y-1', plan.is_popular ? 'border-2 border-emerald-500 shadow-2xl shadow-emerald-500/20 relative' : 'border border-slate-200 shadow-xl shadow-slate-200/50 mt-4 md:mt-4']">
                    
                    <div v-if="plan.is_popular" class="absolute -top-4 left-1/2 -translate-x-1/2 bg-emerald-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                        Most Popular
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-2">{{ plan.name }}</h3>
                    <p class="text-slate-500 text-sm h-10 mb-6">{{ plan.description }}</p>
                    
                    <div class="mb-8">
                        <span class="text-5xl font-black text-slate-900">${{ plan.prices.find(p => p.billing_cycle === 'monthly')?.price || '0' }}</span>
                        <span class="text-slate-500 font-medium">/month</span>
                        <div class="text-xs text-slate-400 mt-1">Billed monthly</div>
                    </div>

                    <Link :href="route('register', { plan: plan.slug })" 
                        :class="['block w-full text-center py-3.5 rounded-xl font-bold transition-colors mb-8', plan.is_popular ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 text-slate-900']">
                        Start Free Trial
                    </Link>

                    <ul class="space-y-4">
                        <li v-for="(feature, idx) in plan.features" :key="idx" class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-slate-600 text-sm">{{ feature }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <h2 class="text-2xl font-bold text-center text-slate-900 mb-10">Frequently Asked Questions</h2>
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-6">
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">Do I need special barcode scanners? <span class="text-slate-400">+</span></h4>
                </div>
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">Is my data secure? <span class="text-slate-400">+</span></h4>
                </div>
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">Can I import my existing stock CSV? <span class="text-slate-400">+</span></h4>
                </div>
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">Can I upgrade or downgrade later? <span class="text-slate-400">+</span></h4>
                </div>
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">How does billing work? <span class="text-slate-400">+</span></h4>
                </div>
                <div class="border-b border-slate-200 pb-4">
                    <h4 class="font-bold text-slate-900 mb-2 flex justify-between">Do you provide training? <span class="text-slate-400">+</span></h4>
                </div>
            </div>
            <div class="text-center mt-12 text-slate-600 text-sm">
                Still have questions? <Link :href="route('contact')" class="text-emerald-600 font-bold hover:underline">Contact our sales team</Link>
            </div>
        </div>
    </PublicLayout>
</template>
