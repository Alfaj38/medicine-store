<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    settings: Object,
    stats: Object,
});

const seoSettings = props.settings?.seo || {};

const form = useForm({
    google_analytics_id: seoSettings.google_analytics_id || '',
    facebook_pixel_id: seoSettings.facebook_pixel_id || '',
    google_search_console_html: seoSettings.google_search_console_html || '',
    meta_title_template: seoSettings.meta_title_template || '',
    meta_description_template: seoSettings.meta_description_template || '',
});

const submit = () => {
    form.post(route('company.seo-analytics.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show success toast
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'SEO settings updated.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
    });
};
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    SEO & Analytics
                </h2>
                <a :href="stats.sitemap_url" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                    View XML Sitemap &rarr;
                </a>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                        <span class="text-sm font-medium text-slate-500 mb-1">Indexed Medicines</span>
                        <span class="text-3xl font-black text-slate-800">{{ stats.total_indexed_medicines }}</span>
                        <div class="mt-4 pt-4 border-t border-slate-50 text-xs text-slate-400">Included in Medicine Sitemap</div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                        <span class="text-sm font-medium text-slate-500 mb-1">Local Landing Pages</span>
                        <span class="text-3xl font-black text-slate-800">{{ stats.local_landing_pages }}</span>
                        <div class="mt-4 pt-4 border-t border-slate-50 text-xs text-slate-400">Optimized for Geo-Search</div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:col-span-2">
                        <span class="text-sm font-medium text-slate-500 mb-1">Sitemap Status</span>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-sm font-bold text-slate-700">Auto-generating daily</span>
                        </div>
                        <div class="text-xs text-slate-500 font-mono bg-slate-100 p-2 rounded truncate">
                            {{ stats.sitemap_url }}
                        </div>
                    </div>
                </div>

                <!-- Forms -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Tracking IDs -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <h3 class="text-lg font-bold text-slate-800">Tracking & Analytics</h3>
                                <p class="text-sm text-slate-500 mt-1">Integrate your storefront with external analytics tools.</p>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID (G-XXXXX)</label>
                                            <input type="text" v-model="form.google_analytics_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="G-1234567890">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Pixel ID</label>
                                            <input type="text" v-model="form.facebook_pixel_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="123456789012345">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Search Console Verification Tag</label>
                                        <input type="text" v-model="form.google_search_console_html" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm" placeholder="<meta name='google-site-verification' content='...' />">
                                        <p class="text-xs text-slate-500 mt-1">Paste the full meta tag provided by Google.</p>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors disabled:opacity-50">
                                            Save Analytics Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Storefront Defaults -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <h3 class="text-lg font-bold text-slate-800">Storefront Meta Defaults</h3>
                                <p class="text-sm text-slate-500 mt-1">Override the default titles and descriptions for your home page.</p>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Page Title</label>
                                        <input type="text" v-model="form.meta_title_template" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Best Pharmacy in Dhaka">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Page Description</label>
                                        <textarea v-model="form.meta_description_template" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Order genuine medicines online..."></textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors disabled:opacity-50">
                                            Save Meta Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Help -->
                    <div class="space-y-6">
                        <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-6">
                            <h4 class="font-bold text-indigo-900 mb-2 text-sm uppercase tracking-wider">Visibility Engine</h4>
                            <p class="text-indigo-800 text-sm leading-relaxed mb-4">
                                Your storefront is powered by an enterprise-grade Visibility Engine. It automatically generates:
                            </p>
                            <ul class="space-y-3 text-sm text-indigo-800">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Product JSON-LD Schemas</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>FAQ Answer Engine Blocks</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Dynamic OpenGraph Social Cards</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Fragmented XML Sitemaps</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AppLayout>
</template>
