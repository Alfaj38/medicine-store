<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    companySlug: String
});
</script>

<template>
    <Head :title="title" />
    
    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-6 text-center">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6 shadow-sm">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </div>
        
        <h1 class="text-3xl font-extrabold text-slate-800 mb-2">{{ title }}</h1>
        <p class="text-slate-500 max-w-md mx-auto mb-8">This module is currently under construction as part of our Multi-Tenant Pharmacy Management platform roadmap.</p>
        
        <Link :href="route('storefront.show', companySlug)" class="inline-flex items-center gap-2 bg-[#0fbc81] text-white px-6 py-3 rounded-xl font-bold shadow-sm hover:bg-emerald-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Pharmacy Store
        </Link>
    </div>
</template>
