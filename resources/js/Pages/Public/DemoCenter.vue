<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const features = [
    {
        title: "Fastest POS Billing in the Market",
        desc: "Scan barcodes, use keyboard shortcuts, and process transactions in under 5 seconds. Perfect for high-traffic hours.",
        image: "pos-billing",
    },
    {
        title: "Bulletproof Batch & Expiry Tracking",
        desc: "Never lose money to expired stock. Get automated alerts when medicines are about to expire and track by specific batch numbers.",
        image: "expiry-tracking",
    },
    {
        title: "Centralized Multi-Branch Control",
        desc: "Running a chain pharmacy? View live stock, transfer items between branches, and monitor overall revenue from a single dashboard.",
        image: "multi-branch",
    },
    {
        title: "Actionable Intelligence & Reports",
        desc: "Know your most profitable items, slow-moving stock, and daily cash flow with our beautiful, real-time analytics suite.",
        image: "reports",
    }
];
</script>

<template>
    <Head title="Demo Center - SaaSMedi" />
    <PublicLayout>
        
        <!-- Header -->
        <div class="bg-slate-900 text-white pt-24 pb-32 px-4 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.05]"></div>
            <div class="max-w-4xl mx-auto text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-sm font-semibold mb-6 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Interactive Demo Center
                </div>
                <h1 class="text-4xl md:text-5xl font-black mb-6">Explore SaaSMedi in Action</h1>
                <p class="text-xl text-slate-400 max-w-2xl mx-auto">Watch our product tour, explore core features, and see exactly why 500+ pharmacies run their entire business on our platform.</p>
            </div>
        </div>

        <!-- Video Player Section -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 mb-24">
            <div class="bg-white p-2 rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-200">
                <div class="aspect-video bg-slate-100 rounded-xl overflow-hidden relative group cursor-pointer flex items-center justify-center border border-slate-200">
                    <!-- Placeholder for actual video embed -->
                    <div class="absolute inset-0 bg-slate-800 mix-blend-multiply opacity-10 group-hover:opacity-20 transition-opacity"></div>
                    <div class="w-20 h-20 bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-600/30 group-hover:scale-110 transition-transform duration-300 z-10">
                        <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <div class="absolute bottom-4 left-4 text-slate-500 font-medium text-sm flex items-center gap-2 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-slate-200">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        2-Minute Product Tour
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Tour -->
        <div class="py-16 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-black text-slate-900">Feature Highlights</h2>
                    <p class="text-slate-500 mt-2">A closer look at the tools that power your pharmacy.</p>
                </div>

                <div class="space-y-24">
                    <div v-for="(feat, i) in features" :key="i" class="grid md:grid-cols-2 gap-12 items-center">
                        <!-- Text -->
                        <div :class="i % 2 !== 0 ? 'md:order-2' : ''">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center font-bold text-xl mb-6 shadow-sm border border-emerald-200">
                                {{ i + 1 }}
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-4">{{ feat.title }}</h3>
                            <p class="text-lg text-slate-600 leading-relaxed">{{ feat.desc }}</p>
                        </div>
                        
                        <!-- Image Placeholder -->
                        <div :class="i % 2 !== 0 ? 'md:order-1' : ''">
                            <div class="bg-white p-4 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 aspect-[4/3] flex flex-col relative overflow-hidden">
                                <!-- Abstract UI Representation based on feature -->
                                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                    <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                    <div class="h-4 w-24 bg-slate-100 rounded ml-4"></div>
                                </div>
                                <div class="flex-1 flex gap-4">
                                    <div class="w-1/4 bg-slate-50 rounded-lg flex flex-col gap-2 p-2 border border-slate-100">
                                        <div v-for="j in 4" :key="j" class="h-6 bg-white border border-slate-100 rounded"></div>
                                    </div>
                                    <div class="w-3/4 bg-slate-50 rounded-lg border border-slate-100 p-4">
                                        <div class="h-8 bg-emerald-50 rounded mb-4 border border-emerald-100 w-1/3"></div>
                                        <div class="space-y-2">
                                            <div v-for="j in 5" :key="j" class="h-8 bg-white border border-slate-100 rounded flex items-center px-2 gap-2">
                                                <div class="w-4 h-4 rounded-sm bg-slate-100"></div>
                                                <div class="h-2 w-1/3 bg-slate-200 rounded"></div>
                                                <div class="h-2 w-1/4 bg-slate-100 rounded ml-auto"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final CTA -->
        <div class="bg-emerald-600 py-24 text-center px-4 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_2px,transparent_2px)] [background-size:32px_32px]"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-6">Ready to see it for yourself?</h2>
                <p class="text-emerald-100 text-lg mb-10">Start your free 14-day trial instantly, or book a personalized 1-on-1 demo with our experts.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('register')" class="bg-white text-emerald-600 hover:bg-slate-50 font-bold px-8 py-4 rounded-full shadow-xl transition-transform hover:-translate-y-1">
                        Start Free Trial
                    </Link>
                    <Link :href="route('book-demo')" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-8 py-4 rounded-full border border-emerald-500 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Book a Live Demo
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
