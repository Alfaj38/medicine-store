<script setup>
import { Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

defineProps({
    company: Object,
    areaName: String,
    seo: Object,
    categories: Array
});
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />

    <StorefrontLayout :company="company">
        <!-- Hero Section optimized for Local SEO -->
        <div class="bg-gradient-to-br from-emerald-50 to-white py-16 md:py-24 border-b border-emerald-100">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 font-bold text-sm mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Serving {{ areaName }}
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight">
                    Fast Medicine Delivery in <span class="text-[#00a669]">{{ areaName }}</span>
                </h1>
                
                <p class="text-lg text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Order genuine medicines online from <strong>{{ company.name }}</strong> and get them delivered straight to your door in {{ areaName }}. Fast, secure, and reliable.
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link :href="route('storefront.medicines', company.slug)" class="bg-[#00a669] hover:bg-emerald-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        Browse All Medicines
                    </Link>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="py-16 bg-white">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Popular Categories</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <Link v-for="category in categories" :key="category.id"
                        :href="route('storefront.medicines', { company: company.slug, category: category.id })"
                        class="bg-slate-50 hover:bg-emerald-50 rounded-2xl p-6 text-center border border-slate-100 hover:border-emerald-200 transition-all group hover:-translate-y-1">
                        <div class="w-12 h-12 mx-auto bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <span class="text-2xl">{{ category.icon || '💊' }}</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-700">{{ category.name }}</h3>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Trust Signals -->
        <div class="py-16 bg-[#f8fafc] border-t border-slate-200">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl font-bold text-slate-900 mb-12">Why {{ areaName }} Chooses Us</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
                        <div class="w-16 h-16 bg-emerald-100 text-[#00a669] rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Fast Local Delivery</h3>
                        <p class="text-slate-600">Quick dispatch to all locations across {{ areaName }}.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
                        <div class="w-16 h-16 bg-emerald-100 text-[#00a669] rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Genuine Medicines</h3>
                        <p class="text-slate-600">100% authentic products sourced directly from manufacturers.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
                        <div class="w-16 h-16 bg-emerald-100 text-[#00a669] rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Expert Support</h3>
                        <p class="text-slate-600">Connect with our verified pharmacists for guidance.</p>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
