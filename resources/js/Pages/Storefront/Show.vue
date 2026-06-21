<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    company: Object
});
</script>

<template>
    <Head :title="company.name" />

    <div class="min-h-screen bg-white font-sans text-slate-800">
        <!-- Top Navbar -->
        <nav class="bg-white border-b border-slate-100 py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-50">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold text-xl overflow-hidden">
                    <img v-if="company.logo" :src="`/storage/${company.logo}`" class="w-full h-full object-contain p-1" />
                    <span v-else>+</span>
                </div>
                <div>
                    <div class="font-extrabold text-lg leading-tight text-emerald-700">{{ company.name }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">Care you can trust</div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors" placeholder="Search medicines, health products...">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <button class="hidden lg:flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload Prescription
                </button>
                <div class="hidden lg:flex items-center gap-1 text-sm font-medium text-slate-600">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Mohammadpur, Dhaka</span>
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm shadow-emerald-200 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    Contact Us
                </button>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-br from-[#e6f7f2] via-[#f0faf7] to-white rounded-[2rem] overflow-hidden shadow-sm">
                <!-- Decorative Swirls -->
                <div class="absolute inset-0 opacity-40 pointer-events-none">
                    <svg class="absolute w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,50 Q25,0 50,50 T100,50" fill="none" stroke="#10b981" stroke-width="0.3" class="opacity-20"/>
                        <path d="M0,70 Q35,20 70,70 T100,70" fill="none" stroke="#14b8a6" stroke-width="0.8" class="opacity-10"/>
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center p-8 lg:p-14 gap-12">
                    <!-- Left: Text -->
                    <div class="flex-1 lg:pr-8">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-emerald-200 text-emerald-700 text-[11px] font-bold mb-6 shadow-sm uppercase tracking-wide">
                            <svg class="w-3.5 h-3.5 bg-emerald-500 text-white rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Verified Pharmacy
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4 tracking-tight">
                            Your <span class="text-emerald-600">Health</span>, Our Priority
                        </h1>
                        <p class="text-slate-600 mb-8 max-w-md text-base leading-relaxed">
                            {{ company.name }} is your trusted partner for genuine medicines, expert advice, and better health every day.
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-x-8 gap-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">100% Genuine</div>
                                    <div class="text-[11px] text-slate-500">Authentic Medicines</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">Expert Support</div>
                                    <div class="text-[11px] text-slate-500">Pharmacist Advice</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">Fast & Reliable</div>
                                    <div class="text-[11px] text-slate-500">Quick & Safe Delivery</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Card -->
                    <div class="lg:w-[420px] relative mt-12 lg:mt-0 flex justify-end">
                        
                        <!-- Company Info Card -->
                        <div class="bg-white rounded-2xl p-6 shadow-xl relative z-10 w-full max-w-sm">
                            <div class="flex items-start gap-4 mb-5">
                                <div class="w-16 h-16 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="company.logo" :src="`/storage/${company.logo}`" class="w-full h-full object-contain p-1" />
                                    <div v-else class="text-emerald-600 font-bold text-2xl">{{ company.name.charAt(0) }}</div>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h2 class="font-extrabold text-xl text-slate-900">{{ company.name }}</h2>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wide">Verified</span>
                                </div>
                                <div class="text-sm text-slate-500 font-medium">{{ company.business_type || 'Wholesale Pharmacy' }}</div>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div v-if="company.email" class="flex items-center gap-3 text-sm text-slate-600">
                                    <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="truncate">{{ company.email }}</span>
                                </div>
                                <div v-if="company.phone" class="flex items-center gap-3 text-sm text-slate-600">
                                    <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <span>{{ company.phone }}</span>
                                </div>
                                <div v-if="company.address" class="flex items-center gap-3 text-sm text-slate-600">
                                    <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    </div>
                                    <span class="line-clamp-2 leading-tight">{{ company.address }}</span>
                                </div>
                            </div>

                            <button class="w-full py-2.5 rounded-xl border-2 border-emerald-100 text-emerald-700 font-bold hover:bg-emerald-50 hover:border-emerald-200 transition-colors text-sm">
                                View on Map
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <!-- Card 1 -->
                <div class="bg-white border border-emerald-100/60 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-9 1V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2H6a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Upload Prescription</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Get your medicines<br>delivered at your door</p>
                        </div>
                    </div>
                    <button class="bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors whitespace-nowrap shadow-sm">Upload Now</button>
                </div>
                <!-- Card 2 -->
                <div class="bg-white border border-emerald-100/60 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Health Products</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Explore our wide range<br>of health essentials</p>
                        </div>
                    </div>
                    <button class="bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors whitespace-nowrap shadow-sm">Shop Now</button>
                </div>
                <!-- Card 3 -->
                <div class="bg-white border border-emerald-100/60 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Wellness Tips</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Daily tips for a<br>healthier you</p>
                        </div>
                    </div>
                    <button class="bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors whitespace-nowrap shadow-sm">Learn More</button>
                </div>
            </div>

            <!-- Middle Section -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-8">
                <!-- Our Branches -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-slate-100/60 relative overflow-hidden">
                    <h3 class="font-bold text-slate-900 text-lg mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        Our Branches
                    </h3>
                    
                    <div class="text-sm text-slate-600 relative z-10">
                        <h4 class="font-bold text-slate-900 text-base">Main Branch</h4>
                        <p class="mt-1.5 mb-3">{{ company.address || 'Location details coming soon.' }}</p>
                        <div v-if="company.phone" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-emerald-100 shadow-sm rounded-full text-xs font-bold text-emerald-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ company.phone }}
                        </div>
                    </div>
                    
                    <!-- Decorative building icon / graphic -->
                    <div class="absolute bottom-4 right-4 text-emerald-100 pointer-events-none">
                        <svg class="w-40 h-40" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100/60 flex flex-col">
                    <h3 class="font-bold text-slate-900 text-sm mb-5">Business Information</h3>
                    <ul class="space-y-4">
                        <li class="grid grid-cols-5 gap-4">
                            <span class="text-slate-500 text-xs col-span-2">Business Type</span>
                            <span class="text-slate-800 text-xs font-medium text-right col-span-3">{{ company.business_type || 'Wholesale Pharmacy' }}</span>
                        </li>
                        <li class="grid grid-cols-5 gap-4">
                            <span class="text-slate-500 text-xs col-span-2">Head Office</span>
                            <span class="text-slate-800 text-xs font-medium text-right col-span-3 truncate">{{ company.address || 'Mohammadpur, Dhaka' }}</span>
                        </li>
                        <li class="grid grid-cols-5 gap-4 items-center">
                            <span class="text-slate-500 text-xs col-span-2">Working Hours</span>
                            <div class="text-right flex items-center justify-end gap-2 col-span-3">
                                <span class="text-slate-800 text-[11px] font-medium">9:00 AM - 10:00 PM</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Open Now</span>
                            </div>
                        </li>
                        <li class="grid grid-cols-5 gap-4">
                            <span class="text-slate-500 text-xs col-span-2">Email</span>
                            <span class="text-slate-800 text-[11px] font-medium text-right col-span-3 truncate">{{ company.email || 'contact@example.com' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Get in Touch -->
                <div class="bg-emerald-500 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/20 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-emerald-600 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h3 class="font-bold text-lg">Get in Touch</h3>
                    </div>
                    <p class="text-emerald-50 text-[11px] mb-6 leading-relaxed">Have questions or need assistance? We're here to help you.</p>
                    <div class="space-y-2 mt-auto">
                        <a :href="`mailto:${company.email}`" class="flex items-center justify-center gap-2 w-full bg-white text-emerald-700 text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Send Email
                        </a>
                        <a :href="`tel:${company.phone}`" class="flex items-center justify-center gap-2 w-full bg-white text-emerald-700 text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Call Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12 py-8 border-t border-slate-100 mb-8">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="text-emerald-500 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900">5000+</div>
                    <div class="text-xs font-medium text-slate-500 mt-0.5">Happy Customers</div>
                </div>
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="text-emerald-500 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900">10000+</div>
                    <div class="text-xs font-medium text-slate-500 mt-0.5">Products</div>
                </div>
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="text-emerald-500 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900">1500+</div>
                    <div class="text-xs font-medium text-slate-500 mt-0.5">Brands</div>
                </div>
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="text-emerald-500 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900">24/7</div>
                    <div class="text-xs font-medium text-slate-500 mt-0.5">Support</div>
                </div>
            </div>
        </div>
    </div>
</template>

