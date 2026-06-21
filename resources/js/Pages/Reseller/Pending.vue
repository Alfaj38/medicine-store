<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Application Pending - MediSaaS Partner" />
    
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 text-center border border-slate-100">
            <div class="w-20 h-20 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <h1 class="text-2xl font-black text-slate-900 mb-4">Application Under Review</h1>
            <p class="text-slate-500 mb-8 leading-relaxed">
                Thank you for applying to the MediSaaS Partner Program! We are currently reviewing your application. You will receive an email once your account is approved.
            </p>
            
            <Link :href="route('home')" class="inline-block font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                ← Return to Homepage
            </Link>
        </div>
    </div>
</template>
