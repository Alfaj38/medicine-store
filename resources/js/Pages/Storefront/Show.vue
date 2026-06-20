<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    company: Object
});
</script>

<template>
    <Head :title="company.name" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Header / Banner -->
        <div class="relative bg-white border-b border-slate-200">
            <div class="absolute inset-0 h-48 bg-gradient-to-r from-emerald-600 to-teal-500">
                <!-- Decorative pattern -->
                <svg class="absolute inset-0 w-full h-full text-white/10" fill="currentColor">
                    <pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="2" />
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#pattern)" />
                </svg>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-8">
                <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 flex flex-col md:flex-row items-center md:items-start gap-8 -mt-8 border border-slate-100">
                    <!-- Logo -->
                    <div class="w-32 h-32 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img v-if="company.logo" :src="`/storage/${company.logo}`" class="w-full h-full object-contain p-2" :alt="company.name">
                        <div v-else class="text-emerald-600 font-bold text-4xl">{{ company.name.charAt(0) }}</div>
                    </div>
                    
                    <!-- Company Info -->
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                            <h1 class="text-3xl font-extrabold text-slate-900">{{ company.name }}</h1>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Verified</span>
                        </div>
                        
                        <p v-if="company.business_type" class="text-sm font-medium text-slate-500 mb-4">{{ company.business_type }}</p>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm text-slate-600">
                            <div v-if="company.email" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ company.email }}
                            </div>
                            <div v-if="company.phone" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ company.phone }}
                            </div>
                            <div v-if="company.website" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                <a :href="company.website" target="_blank" class="hover:text-emerald-600 transition-colors">{{ company.website }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: About & Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        About Us
                    </h2>
                    <div v-if="company.about_company" class="text-slate-600 leading-relaxed whitespace-pre-wrap">
                        {{ company.about_company }}
                    </div>
                    <div v-else class="text-slate-400 italic">
                        No description provided yet.
                    </div>
                </div>

                <!-- Branches Section -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Our Branches
                    </h2>
                    
                    <div v-if="company.branches && company.branches.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="branch in company.branches" :key="branch.id" class="border border-slate-100 bg-slate-50 rounded-2xl p-4 hover:border-emerald-200 transition-colors group">
                            <h3 class="font-bold text-slate-800 mb-1 group-hover:text-emerald-700 transition-colors">{{ branch.name }}</h3>
                            <p v-if="branch.address" class="text-xs text-slate-500 mb-2 flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                {{ branch.address }}
                            </p>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span v-if="branch.phone" class="inline-flex items-center px-2 py-1 rounded bg-white border border-slate-200 text-[10px] font-medium text-slate-600">
                                    <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ branch.phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 bg-slate-50 rounded-2xl border border-slate-100 border-dashed text-sm text-slate-500">
                        No branches available for display.
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-8">
                <!-- Business Information -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-100 pb-3">Business Information</h3>
                    <ul class="space-y-4">
                        <li v-if="company.year_established">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Established</div>
                            <div class="text-sm font-medium text-slate-800">{{ company.year_established }}</div>
                        </li>
                        <li v-if="company.registration_number">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Registration No</div>
                            <div class="text-sm font-medium text-slate-800">{{ company.registration_number }}</div>
                        </li>
                        <li v-if="company.trade_license_number">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Trade License</div>
                            <div class="text-sm font-medium text-slate-800">{{ company.trade_license_number }}</div>
                        </li>
                        <li v-if="company.alternative_phone">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Alt. Phone</div>
                            <div class="text-sm font-medium text-slate-800">{{ company.alternative_phone }}</div>
                        </li>
                        <li v-if="company.address">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Head Office</div>
                            <div class="text-sm font-medium text-slate-800">{{ company.address }}</div>
                        </li>
                    </ul>
                </div>

                <!-- Contact CTA -->
                <div class="bg-emerald-600 rounded-3xl p-6 text-white text-center shadow-lg shadow-emerald-600/20">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Get in Touch</h3>
                    <p class="text-emerald-100 text-sm mb-6">Have questions or need assistance? Contact us directly.</p>
                    <a :href="`mailto:${company.email}`" class="block w-full bg-white text-emerald-700 font-bold py-3 px-4 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm">
                        Send Email
                    </a>
                </div>
            </div>

        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 mt-12 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center justify-center">
                <div class="flex items-center gap-2 mb-2 opacity-50">
                    <div class="w-6 h-6 rounded bg-slate-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="font-bold text-xs text-slate-800 tracking-tight">Pharma Point Platform</span>
                </div>
                <p class="text-[10px] text-slate-400">Powered by Pharma Point Management System</p>
            </div>
        </footer>
    </div>
</template>
