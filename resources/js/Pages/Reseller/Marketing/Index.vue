<script setup>
import { Head } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';
</script>

<template>
    <Head title="Marketing Resources - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Marketing Resources</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Download official banners, social media kits, and promotional copy.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Brand Kit -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group cursor-pointer hover:border-[#00b67a]/30 transition-colors">
                <div class="h-40 bg-slate-50 border-b border-slate-100 flex items-center justify-center p-6 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#00b67a]/5 to-transparent"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-[#00b67a] relative z-10 border border-emerald-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 mb-1">Official Brand Kit</h3>
                    <p class="text-[12px] text-slate-500 font-medium mb-4">Logos, color palettes, and typography guidelines for SaaSMedi.</p>
                    <a :href="route('reseller.marketing.brand-kit')" class="block w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs transition-colors text-center" download>Download ZIP</a>
                </div>
            </div>

            <!-- Social Media Banners -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group cursor-pointer hover:border-[#00b67a]/30 transition-colors">
                <div class="h-40 bg-slate-50 border-b border-slate-100 flex items-center justify-center p-6 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-blue-500 relative z-10 border border-blue-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 mb-1">Social Media Pack</h3>
                    <p class="text-[12px] text-slate-500 font-medium mb-4">Ready-to-use banners for Facebook, LinkedIn, and Twitter.</p>
                    <a :href="route('reseller.marketing.social-pack')" class="block w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs transition-colors text-center" download>Download ZIP</a>
                </div>
            </div>
            
            <!-- Email Templates -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group cursor-pointer hover:border-[#00b67a]/30 transition-colors">
                <div class="h-40 bg-slate-50 border-b border-slate-100 flex items-center justify-center p-6 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-amber-500 relative z-10 border border-amber-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 mb-1">Email Campaign Copy</h3>
                    <p class="text-[12px] text-slate-500 font-medium mb-4">High-converting email templates for pharmacy owners.</p>
                    <a :href="route('reseller.marketing.email-templates')" class="block w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs transition-colors text-center" download>View Templates</a>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
