<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    company: Object,
    categories: Array,
    medicines: Object,
    filters: Object,
    seo: Object,
});
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <StorefrontLayout :company="company" :initial-search-query="filters?.q">

        <div class="w-full">
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-br from-[#e1f5ed] to-[#f4fcf9] overflow-hidden w-full">
                <!-- Decorative Abstract Shapes matching the image -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <svg class="absolute w-[200%] h-[200%] -top-[50%] -left-[50%] opacity-60" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice">
                        <path d="M10,100 C40,50 160,50 190,100 C220,150 160,200 100,200 C40,200 -20,150 10,100 Z" fill="none" stroke="#a7f3d0" stroke-width="3" class="opacity-30 transform rotate-12 origin-center blur-[2px]" />
                        <path d="M30,100 C70,40 130,40 170,100 C210,160 130,220 100,220 C70,220 10,160 30,100 Z" fill="none" stroke="#a7f3d0" stroke-width="6" class="opacity-20 transform -rotate-6 origin-center blur-[4px]" />
                        <path d="M50,100 C90,20 110,20 150,100 C190,180 110,240 100,240 C90,240 10,180 50,100 Z" fill="none" stroke="#047857" stroke-width="12" class="opacity-5 transform rotate-45 origin-center blur-[8px]" />
                        <path d="M0,150 C60,80 180,80 200,150 C240,220 180,280 120,280 C60,280 0,220 0,150 Z" fill="none" stroke="#34d399" stroke-width="1" class="opacity-40 transform rotate-[-15deg] origin-center blur-[1px]" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between py-6 px-4 sm:px-10 lg:py-6 lg:px-16 gap-8 max-w-[1400px] mx-auto w-full">
                    <!-- Left: Text -->
                    <div class="flex-1 lg:pr-8 max-w-[650px]">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-emerald-100 text-[#0fbc81] text-[11px] font-bold mb-4 shadow-sm">
                            <div class="bg-[#0fbc81] rounded-full w-4 h-4 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            Verified Pharmacy
                        </div>
                        
                        <h1 class="text-4xl lg:text-[42px] xl:text-[46px] font-extrabold text-[#1a202c] leading-[1.1] mb-3 tracking-tight lg:whitespace-nowrap">
                            Your <span class="text-[#0fbc81]">Health</span>, Our Priority
                        </h1>
                        
                        <p class="text-[#4a5568] mb-6 text-[15px] leading-relaxed pr-8 font-medium max-w-lg">
                            {{ company.name }} is your trusted partner for genuine medicines, expert advice, and better health every day.
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-x-8 gap-y-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <div>
                                    <div class="font-bold text-[#1a202c] text-[13px]">100% Genuine</div>
                                    <div class="text-[11px] text-slate-500">Authentic Medicines</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                <div>
                                    <div class="font-bold text-[#1a202c] text-[13px]">Expert Support</div>
                                    <div class="text-[11px] text-slate-500">Pharmacist Advice</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <div>
                                    <div class="font-bold text-[#1a202c] text-[13px]">Fast & Reliable</div>
                                    <div class="text-[11px] text-slate-500">Quick & Safe Delivery</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Illustrations and Card -->
                    <div class="lg:w-[500px] relative flex justify-end items-center h-full">
                        
                        <!-- Medicine Illustration Image -->
                        <div class="absolute left-[-171px] top-1/2 -translate-y-1/2 w-[400px] pointer-events-none hidden lg:block z-0">
                            <img src="/images/medicine_sample.png" alt="Medicine Display" class="w-full h-auto drop-shadow-[0_20px_50px_rgba(0,0,0,0.15)]" />
                        </div>

                        <!-- Company Info Card (Matching exactly) -->
                        <div class="bg-white rounded-[24px] p-5 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] relative z-40 w-full max-w-[360px] transform lg:translate-x-8 border border-white">
                            <div class="flex items-start mb-3">
                                <div class="w-14 h-14 bg-[#f8fafc] rounded-xl border border-slate-100 flex flex-col items-center justify-center p-2">
                                    <img v-if="company.logo" :src="`/storage/${company.logo}`" class="w-full h-full object-contain mb-1" />
                                    <template v-else>
                                        <div class="text-[8px] font-bold text-slate-400 text-center uppercase tracking-wider mb-1">Image</div>
                                        <div class="text-[10px] font-bold text-slate-700 text-center leading-tight">Pharma<br>Point</div>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <h2 class="font-extrabold text-[20px] text-[#1a202c]">{{ company.name }}</h2>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#eaf8f4] text-[#0fbc81] uppercase tracking-wider">Verified</span>
                                </div>
                                <div class="text-[12px] text-slate-500 font-medium">{{ company.business_type || 'Wholesale Pharmacy' }}</div>
                            </div>

                            <div class="space-y-2.5 mb-5 mt-1">
                                <div class="flex items-center gap-3 text-[13px] text-slate-600">
                                    <svg class="w-[16px] h-[16px] text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="truncate font-medium">{{ company.email || 'mmamun.roshid@gmail.com' }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-[13px] text-slate-600">
                                    <svg class="w-[16px] h-[16px] text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span class="font-medium">{{ company.phone || '01918828282' }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-[13px] text-slate-600">
                                    <svg class="w-[16px] h-[16px] text-[#0fbc81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span class="truncate font-medium">{{ company.address || 'Mohammadpur, Dhaka' }}</span>
                                </div>
                            </div>

                            <button class="w-full py-2 rounded-xl border border-[#0fbc81] text-[#0fbc81] font-bold hover:bg-[#eaf8f4] transition-colors text-[13px]">
                                View on Map
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Combined Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6 mt-6 lg:mt-8">
                <!-- Left 3 Columns -->
                <div class="lg:col-span-3 flex flex-col gap-4 lg:gap-6">
                    <!-- Action Cards Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
                        <!-- Card 1 -->
                        <div class="bg-[#f0fbf7] border border-[#0fbc81] rounded-[20px] p-4 lg:p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow h-[96px]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#0fbc81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-3-3v6m-9 1V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2H6a2 2 0 01-2-2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0fbc81] text-[14px]">Upload Prescription</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-[1.3]">Get your medicines<br>delivered at your door</p>
                                </div>
                            </div>
                            <Link :href="route('storefront.prescription', company.slug)" class="bg-[#0fbc81] text-white text-[11px] font-bold px-3.5 py-2 rounded-lg shadow-sm hover:bg-emerald-600 transition-colors whitespace-nowrap">Upload Now</Link>
                        </div>
                        <!-- Card 2 -->
                        <div class="bg-white border border-slate-200 rounded-[20px] p-4 lg:p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow h-[96px]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#0fbc81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0fbc81] text-[14px]">Health Products</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-[1.3]">Explore our wide range<br>of health essentials</p>
                                </div>
                            </div>
                            <Link :href="route('storefront.medicines', company.slug)" class="bg-[#0fbc81] text-white text-[11px] font-bold px-3.5 py-2 rounded-lg shadow-sm hover:bg-emerald-600 transition-colors whitespace-nowrap">Shop Now</Link>
                        </div>
                        <!-- Card 3 -->
                        <div class="bg-white border border-slate-200 rounded-[20px] p-4 lg:p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow h-[96px]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#0fbc81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0fbc81] text-[14px]">Wellness Tips</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-[1.3]">Daily tips for a<br>healthier you</p>
                                </div>
                            </div>
                            <Link :href="route('storefront.blog', company.slug)" class="bg-[#0fbc81] text-white text-[11px] font-bold px-3.5 py-2 rounded-lg shadow-sm hover:bg-emerald-600 transition-colors whitespace-nowrap">Learn More</Link>
                        </div>
                    </div>

                    <!-- Middle Section Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
                        <!-- Our Branches -->
                        <div class="md:col-span-2 bg-white rounded-[20px] p-6 lg:p-7 shadow-sm border border-slate-200 relative overflow-hidden flex flex-col justify-between min-h-[230px] lg:h-[230px]">
                            <h3 class="font-bold text-[#1a202c] text-[17px] mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#0fbc81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                Our Branches
                            </h3>
                            
                            <div class="bg-[#f8fbfb] rounded-[16px] p-5 flex-1 relative overflow-hidden flex items-center">
                                <div class="relative z-10 w-full max-w-sm">
                                    <h4 class="font-bold text-[#1a202c] text-[15px]">Main Branch</h4>
                                    <p class="mt-1 mb-4 text-[13px] text-slate-500">Mohammadpur, Dhaka</p>
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-[#0fbc81] rounded-lg shadow-sm text-[12px] font-medium text-[#1a202c] cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-[#0fbc81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        01918828282
                                    </div>
                                </div>
                                
                                <!-- Decorative building graphic -->
                                <div class="absolute right-4 bottom-2 flex items-end">
                                    <div class="w-[120px] h-[85px] bg-[#d1f4e5] rounded-t-xl relative border-b-[6px] border-[#a7e8ce]">
                                        <div class="absolute top-2 left-1/2 -translate-x-1/2 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm">
                                            <svg class="w-4 h-4 text-[#86d9b6]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 10h-5V5h-4v5H5v4h5v5h4v-5h5v-4z"></path></svg>
                                        </div>
                                        <div class="absolute bottom-0 left-5 w-7 h-10 bg-[#a7e8ce] rounded-t-sm shadow-inner"></div>
                                        <div class="absolute bottom-0 left-[60px] w-10 h-12 bg-[#eaf8f4] rounded-t shadow-sm"></div>
                                        
                                        <!-- Trees -->
                                        <div class="absolute -left-5 bottom-0 w-6 h-12 bg-[#a7e8ce] rounded-full border-b-[4px] border-[#86d9b6]"></div>
                                    </div>
                                    <div class="w-[60px] h-[65px] bg-[#eaf8f4] rounded-t-xl relative border-l-4 border-[#cbf2e3] border-b-[6px] border-[#cbf2e3] -ml-2 z-10 shadow-[-4px_0_15px_rgba(0,0,0,0.02)]">
                                        <div class="absolute bottom-0 left-3 w-5 h-8 bg-[#cbf2e3] rounded-t-sm shadow-inner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="bg-white rounded-[20px] p-6 lg:p-7 shadow-sm border border-slate-200 flex flex-col h-[230px]">
                            <h3 class="font-bold text-[#1a202c] text-[15px] mb-5">Business Information</h3>
                            <ul class="space-y-4">
                                <li class="grid grid-cols-5 gap-3">
                                    <span class="text-slate-400 text-[12px] col-span-2">Business Type</span>
                                    <span class="text-slate-600 text-[12px] font-medium col-span-3">{{ company.business_type || 'Wholesale Pharmacy' }}</span>
                                </li>
                                <li class="grid grid-cols-5 gap-3">
                                    <span class="text-slate-400 text-[12px] col-span-2">Head Office</span>
                                    <span class="text-slate-600 text-[12px] font-medium col-span-3 truncate">{{ company.address || 'Mohammadpur, Dhaka' }}</span>
                                </li>
                                <li class="grid grid-cols-5 gap-3 items-center">
                                    <span class="text-slate-400 text-[12px] col-span-2">Working Hours</span>
                                    <div class="flex items-center gap-2 col-span-3">
                                        <span class="text-slate-600 text-[12px] font-medium whitespace-nowrap">9:00 AM - 10:00 PM</span>
                                        <span class="text-[9px] font-bold text-[#0fbc81] bg-[#eaf8f4] px-1.5 py-0.5 rounded uppercase tracking-wider">Open Now</span>
                                    </div>
                                </li>
                                <li class="grid grid-cols-5 gap-3">
                                    <span class="text-slate-400 text-[12px] col-span-2">Email</span>
                                    <span class="text-slate-600 text-[12px] font-medium col-span-3 truncate">{{ company.email || 'mmamun.roshid@gmail.com' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Get in Touch -->
                <div class="lg:col-span-1 bg-[#0fbc81] rounded-[24px] p-6 lg:p-8 text-white shadow-md flex flex-col h-[350px] lg:h-full relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full blur-2xl transform translate-x-10 -translate-y-10"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-4 mt-2">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shadow-inner relative">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-[#0fbc81]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </div>
                            </div>
                            <h3 class="font-bold text-[18px]">Get in Touch</h3>
                        </div>
                        <p class="text-white/90 text-[13px] mb-8 pr-2 leading-relaxed font-medium">Have questions or need assistance? We're here to help you.</p>
                        
                        <div class="space-y-3 mt-auto mb-2">
                            <button class="flex items-center justify-center gap-2 w-full bg-white text-[#0fbc81] text-[14px] font-bold py-3.5 px-4 rounded-[14px] shadow-sm hover:bg-[#f8fafc] transition-colors">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Send Email
                            </button>
                            <button class="flex items-center justify-center gap-2 w-full bg-white text-[#0fbc81] text-[14px] font-bold py-3.5 px-4 rounded-[14px] shadow-sm hover:bg-[#f8fafc] transition-colors">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Call Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Stats Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mt-12 mb-8 pt-8 px-8 border-t border-slate-200">
                <div class="flex items-center justify-start gap-4">
                    <div class="text-[#0fbc81]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-[22px] font-extrabold text-[#1a202c] leading-none mb-1">5000+</div>
                        <div class="text-[13px] text-slate-500 font-medium">Happy Customers</div>
                    </div>
                </div>
                <div class="flex items-center justify-start gap-4">
                    <div class="text-[#0fbc81]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-[22px] font-extrabold text-[#1a202c] leading-none mb-1">10000+</div>
                        <div class="text-[13px] text-slate-500 font-medium">Orders Delivered</div>
                    </div>
                </div>
                <div class="flex items-center justify-start gap-4">
                    <div class="text-[#0fbc81]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-[22px] font-extrabold text-[#1a202c] leading-none mb-1">1500+</div>
                        <div class="text-[13px] text-slate-500 font-medium">Medicines</div>
                    </div>
                </div>
                <div class="flex items-center justify-start gap-4">
                    <div class="text-[#0fbc81]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-[22px] font-extrabold text-[#1a202c] leading-none mb-1">24/7</div>
                        <div class="text-[13px] text-slate-500 font-medium">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
