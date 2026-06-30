<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    seo: Object
});

const activeFeature = ref(1);

const features = [
    {
        id: 1,
        title: 'Smart Point of Sale (POS)',
        desc: 'Fast billing with barcode support, keyboard shortcuts, and multiple payment options in one screen.',
        icon: '💳',
        color: 'emerald',
        popular: true
    },
    {
        id: 2,
        title: 'Inventory & Ledger',
        desc: 'Track inventory, automate expiry alerts, low stock warnings, and real-time stock ledgers.',
        icon: '📦',
        color: 'orange'
    },
    {
        id: 3,
        title: 'Multi-Branch & Users',
        desc: 'Centralized management. Assign roles (Cashier, Manager) and control exact permissions per user.',
        icon: '🏢',
        color: 'blue'
    },
    {
        id: 4,
        title: 'Sales & Purchase Management',
        desc: 'Manage purchases, sales, returns, and suppliers with detailed insights.',
        icon: '🛒',
        color: 'indigo'
    },
    {
        id: 5,
        title: 'Reports & Analytics',
        desc: 'Get real-time business insights with beautiful reports and analytics.',
        icon: '📈',
        color: 'rose'
    }
];

// POS state mockup
const items = ref([
    { id: 1, name: 'Napa Extra', desc: 'Paracetamol 500mg', price: 2.50 },
    { id: 2, name: 'Xepa 500mg', desc: 'Ciprofloxacin', price: 5.40 },
    { id: 3, name: 'Nizoral', desc: 'Ketoconazole 100mg', price: 9.30 },
]);
const cart = ref([{ item: items.value[0], qty: 1 }]);
const subtotal = computed(() => cart.value.reduce((acc, c) => acc + (c.item.price * c.qty), 0));

// Inventory state
const inventory = [
    { name: 'Napa Extra', stock: 1540, expiry: '2027-12-01', status: 'Good' },
    { name: 'Seclo 20mg', stock: 45, expiry: '2026-08-15', status: 'Low' },
    { name: 'Alatrol', stock: 8, expiry: '2026-07-10', status: 'Critical' },
];

</script>

<template>
    <Head :title="seo?.title || 'Features - SaaSMedi'" />
    <PublicLayout>
        <!-- Header -->
        <div class="bg-slate-50/30 pt-32 pb-12 relative overflow-hidden">
            <!-- Decorative floating elements -->
            <div class="absolute top-20 left-[15%] w-16 h-16 transform -rotate-12 bg-white rounded-full shadow-xl flex items-center justify-center text-3xl animate-bounce" style="animation-duration: 3s;">💊</div>
            <div class="absolute top-32 right-[15%] w-20 h-20 transform rotate-12 bg-emerald-500 rounded-3xl shadow-2xl shadow-emerald-500/30 flex items-center justify-center text-white text-4xl animate-bounce" style="animation-duration: 4s;">🛡️</div>

            <div class="text-center px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="inline-block bg-emerald-50 text-emerald-600 font-bold text-xs tracking-widest uppercase px-4 py-1.5 rounded-full mb-6">Features</div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 max-w-3xl mx-auto tracking-tight">Everything You Need to <br/><span class="text-emerald-500">Run Your Pharmacy Smarter</span></h1>
                <p class="text-[15px] font-medium text-slate-500 max-w-2xl mx-auto">Powerful modules built specifically for the modern pharmacy business.</p>
            </div>
        </div>

        <!-- Main Features Split Layout -->
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-start">
                
                <!-- Feature List (Left) -->
                <div class="lg:col-span-5 space-y-3 relative z-20">
                    <div v-for="feat in features" :key="feat.id" 
                        @click="activeFeature = feat.id" 
                        :class="[
                            'p-5 rounded-2xl border-2 transition-all cursor-pointer flex items-center justify-between group', 
                            activeFeature === feat.id 
                                ? 'bg-white border-emerald-500 shadow-xl shadow-emerald-500/10' 
                                : 'bg-white border-slate-100 hover:border-emerald-200'
                        ]">
                        <div class="flex items-start gap-4">
                            <div :class="[
                                'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl transition-colors', 
                                activeFeature === feat.id 
                                    ? `bg-emerald-100 text-emerald-600` 
                                    : 'bg-slate-50 text-slate-400 group-hover:bg-slate-100'
                            ]">{{ feat.icon }}</div>
                            <div>
                                <h3 class="text-[15px] font-bold text-slate-900 mb-1 flex items-center gap-2">
                                    {{ feat.title }}
                                    <span v-if="feat.popular" class="bg-emerald-100 text-emerald-600 text-[9px] px-2 py-0.5 rounded-full uppercase tracking-wider">Popular</span>
                                </h3>
                                <p class="text-slate-500 text-[12px] leading-relaxed pr-4">{{ feat.desc }}</p>
                            </div>
                        </div>
                        <svg :class="['w-5 h-5 flex-shrink-0 transition-transform', activeFeature === feat.id ? 'text-emerald-500 translate-x-1' : 'text-slate-300']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>

                    <!-- Trusted Badge -->
                    <div class="pt-6 flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-600">Trusted by 500+ pharmacies worldwide</span>
                    </div>
                </div>

                <!-- Visuals Mockup (Right) -->
                <div class="lg:col-span-7 mt-12 lg:mt-0 sticky top-32 z-10">
                    <div class="relative rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-slate-200/50 overflow-hidden flex flex-col aspect-[4/3] max-h-[600px] transition-all duration-500">
                        <!-- Mac OS Header -->
                        <div class="h-12 border-b border-slate-100 flex items-center px-4 bg-slate-50 relative">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span class="text-xs font-bold text-slate-600">{{ features.find(f => f.id === activeFeature)?.title }}</span>
                            </div>
                        </div>
                        
                        <!-- Dynamic Content Area -->
                        <div class="flex-1 bg-slate-50/50 p-6 overflow-hidden">
                            
                            <!-- 1. POS Mockup -->
                            <div v-if="activeFeature === 1" class="h-full flex gap-6 animate-fade-in">
                                <!-- Left side: Products -->
                                <div class="flex-[3] bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex flex-col">
                                    <div class="text-xs font-bold text-slate-800 mb-4 pb-2 border-b border-slate-50">Medicine (Click to Add)</div>
                                    <div class="space-y-2 overflow-y-auto pr-2">
                                        <div v-for="item in items" :key="item.id" class="flex justify-between items-center p-3 rounded-lg border border-slate-100 hover:border-emerald-200 hover:shadow-md cursor-pointer transition-all group">
                                            <div>
                                                <div class="text-[13px] font-bold text-slate-800 group-hover:text-emerald-600">{{ item.name }}</div>
                                                <div class="text-[10px] font-medium text-slate-400 mt-0.5">{{ item.desc }}</div>
                                            </div>
                                            <div class="text-[13px] font-black text-emerald-600">${{ item.price.toFixed(2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right side: Cart -->
                                <div class="flex-[2] bg-white rounded-xl border border-slate-100 shadow-sm flex flex-col relative overflow-hidden">
                                    <div class="p-4 flex-1 flex flex-col">
                                        <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-50">
                                            <span class="text-xs font-bold text-slate-800">Cart</span>
                                            <span class="text-[10px] font-bold text-slate-400 hover:text-rose-500 cursor-pointer">Clear</span>
                                        </div>
                                        <div class="flex-1 space-y-3">
                                            <div v-for="(c, idx) in cart" :key="idx" class="flex justify-between items-center bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                <div class="text-[11px] font-bold text-slate-700"><span class="text-emerald-600">1x</span> {{ c.item.name }}</div>
                                                <div class="text-[11px] font-bold text-slate-800">${{ (c.item.price * c.qty).toFixed(2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                                        <div class="flex justify-between items-end mb-4">
                                            <span class="text-sm font-bold text-slate-800">Total</span>
                                            <span class="text-2xl font-black text-emerald-600">${{ subtotal.toFixed(2) }}</span>
                                        </div>
                                        <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold py-3 rounded-lg shadow-lg shadow-emerald-500/20 transition-transform active:scale-95">Pay (F9)</button>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Inventory Mockup -->
                            <div v-else-if="activeFeature === 2" class="h-full bg-white rounded-xl border border-slate-100 shadow-sm p-4 animate-fade-in flex flex-col">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="relative w-64">
                                        <input type="text" class="w-full rounded-lg border-slate-200 text-xs py-2 pl-8" placeholder="Search inventory..." readonly>
                                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <div class="px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-lg border border-orange-200">2 Alerts</div>
                                </div>
                                <div class="border border-slate-100 rounded-lg overflow-hidden flex-1">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                                            <tr>
                                                <th class="px-4 py-3 font-bold">Item Name</th>
                                                <th class="px-4 py-3 font-bold">Stock Quantity</th>
                                                <th class="px-4 py-3 font-bold">Expiry Date</th>
                                                <th class="px-4 py-3 font-bold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="inv in inventory" :key="inv.name" class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                                                <td class="px-4 py-3.5 font-bold text-slate-800">{{ inv.name }}</td>
                                                <td class="px-4 py-3.5 font-bold text-slate-600">{{ inv.stock }} Units</td>
                                                <td class="px-4 py-3.5 font-medium text-slate-500">{{ inv.expiry }}</td>
                                                <td class="px-4 py-3.5">
                                                    <span v-if="inv.status === 'Good'" class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">Good</span>
                                                    <span v-else-if="inv.status === 'Low'" class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded text-[10px] font-bold">Low Stock</span>
                                                    <span v-else class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded text-[10px] font-bold">Critical</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Placeholder for other tabs to keep the UI interactive -->
                            <div v-else class="h-full bg-white rounded-xl border border-slate-100 shadow-sm flex items-center justify-center flex-col animate-fade-in text-center p-8">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-3xl mb-4">{{ features.find(f => f.id === activeFeature)?.icon }}</div>
                                <h3 class="text-lg font-bold text-slate-800 mb-2">{{ features.find(f => f.id === activeFeature)?.title }}</h3>
                                <p class="text-sm text-slate-500 max-w-sm">{{ features.find(f => f.id === activeFeature)?.desc }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infrastructure bottom bar -->
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 pb-24 mt-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-xl shadow-slate-200/40">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="flex items-start gap-4 p-2">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-[13px] mb-1">Faster Billing</h4>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Save time & serve more customers</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-2">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-[13px] mb-1">Secure & Reliable</h4>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Your data is 100% safe and encrypted</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-2">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-[13px] mb-1">Cloud Based</h4>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Access your business from anywhere</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-2">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-[13px] mb-1">24/7 Support</h4>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">We're here to help anytime you need</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
