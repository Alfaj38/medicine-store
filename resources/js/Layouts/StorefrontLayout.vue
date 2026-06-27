<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { useCart } from '@/Composables/useCart';

const props = defineProps({
    company: Object,
    initialSearchQuery: String,
    hideCart: Boolean,
});

const searchQuery = ref(props.initialSearchQuery || '');

const { cartCount, cartTotal, clearCart } = useCart();

const performSearch = debounce(() => {
    router.get(route('storefront.search', props.company.slug), {
        q: searchQuery.value,
    });
}, 500);

const checkout = () => {
    if (cartCount.value === 0) return;
    router.get(route('storefront.checkout', props.company.slug));
};
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] font-sans text-[#1a202c] pb-20">
        <!-- Top Navbar -->
        <nav class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-50">
            <!-- Main Navbar Row -->
            <div class="py-3 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="max-w-[1400px] mx-auto w-full flex items-center justify-between">
                    <!-- Logo -->
                    <Link :href="route('storefront.show', company.slug)" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 10h-5V5h-4v5H5v4h5v5h4v-5h5v-4z"></path></svg>
                        </div>
                        <div class="flex flex-col">
                            <div class="font-extrabold text-[17px] leading-tight text-emerald-600 tracking-tight">{{ company.name || 'Pharma Point' }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">Care you can trust</div>
                        </div>
                    </Link>

                    <!-- Desktop Search Bar -->
                    <div class="hidden md:flex flex-1 max-w-xl mx-8">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="search" name="global_store_search" autocomplete="off" spellcheck="false" v-model="searchQuery" @input="performSearch" class="block w-full pl-11 pr-4 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-all" placeholder="Search medicines, health products...">
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-3 md:gap-4 lg:gap-6">
                        <a href="/" class="hidden lg:flex items-center gap-2 text-sm font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-4 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Main Home
                        </a>
                        
                        <Link :href="route('storefront.prescription', company.slug)" class="hidden lg:flex items-center gap-2 text-sm font-semibold text-slate-700 bg-white border border-emerald-200 px-4 py-2 rounded-xl hover:bg-emerald-50 transition-colors">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Upload Prescription
                        </Link>
                        
                        <div class="hidden xl:flex items-center gap-1.5 text-sm font-medium text-slate-600 cursor-pointer hover:text-emerald-600 transition-colors">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ company.address || 'Mohammadpur, Dhaka' }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        
                        <Link :href="route('storefront.contact', company.slug)" class="bg-[#0fbc81] hover:bg-emerald-600 text-white px-4 py-2 md:px-5 md:py-2.5 rounded-xl text-xs md:text-sm font-semibold shadow-md shadow-emerald-200 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Contact Us
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="md:hidden px-4 pb-3">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="search" name="mobile_store_search" autocomplete="off" spellcheck="false" v-model="searchQuery" @input="performSearch" class="block w-full pl-11 pr-4 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all" placeholder="Search medicines, health products...">
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Floating Cart Widget -->
        <div v-if="cartCount > 0 && !hideCart" class="fixed bottom-6 right-6 z-50">
            <div class="bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] border border-slate-100 p-4 w-[320px] flex flex-col transform transition-all">
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-[#0fbc81] rounded-lg flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-[#1a202c] leading-tight">Your Cart</div>
                            <div class="text-[11px] text-slate-500 font-medium">{{ cartCount }} {{ cartCount === 1 ? 'item' : 'items' }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Total</div>
                        <div class="font-extrabold text-[#0fbc81] text-[16px]">৳{{ cartTotal.toFixed(2) }}</div>
                    </div>
                </div>
                <button @click="checkout" class="w-full bg-[#1a202c] text-white py-3 rounded-xl font-bold text-[14px] hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                    Proceed to Checkout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</template>
