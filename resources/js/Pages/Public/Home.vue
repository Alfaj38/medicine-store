<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, computed } from 'vue';

const items = ref([
    { id: 1, name: 'Napa Extra', desc: 'Paracetamol 500mg', price: 2.50, color: 'emerald' },
    { id: 2, name: 'Seclo 20mg', desc: 'Omeprazole', price: 5.00, color: 'blue' },
    { id: 3, name: 'Alatrol', desc: 'Cetirizine 10mg', price: 1.50, color: 'orange' },
    { id: 4, name: 'Fexo 120mg', desc: 'Fexofenadine', price: 3.00, color: 'purple' },
    { id: 5, name: 'Ceevit', desc: 'Vitamin C 250mg', price: 1.20, color: 'rose' },
]);

const cart = ref([
    { item: items.value[0], qty: 1 },
    { item: items.value[1], qty: 2 }
]);

const addToCart = (item) => {
    const existing = cart.value.find(c => c.item.id === item.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.value.push({ item, qty: 1 });
    }
};

const subtotal = computed(() => cart.value.reduce((acc, c) => acc + (c.item.price * c.qty), 0));
</script>

<template>
    <Head title="Pharmacy POS & ERP Software" />
    <PublicLayout>
        
        <!-- Hero Section -->
        <div class="relative bg-slate-50/50 pt-20 pb-24 overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mr-48 -mt-48 w-[800px] h-[800px] bg-emerald-50 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 -ml-48 -mb-48 w-[600px] h-[600px] bg-teal-50 rounded-full blur-3xl opacity-60"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                    
                    <!-- Left Content -->
                    <div class="lg:col-span-5 mb-16 lg:mb-0">
                        <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-bold tracking-wide uppercase mb-6">
                            All-in-One Pharmacy Management System
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-[54px] font-extrabold text-slate-900 leading-[1.15] mb-6 tracking-tight">
                            The Complete Cloud<br/>
                            POS & ERP for<br/>
                            <span class="text-emerald-500">Modern Pharmacies</span>
                        </h1>
                        <p class="text-[15px] text-slate-600 mb-8 max-w-lg leading-relaxed font-medium">
                            Manage sales, inventory, purchases, and multi-branches effortlessly. Increase efficiency, reduce losses, and grow your pharmacy business.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-8">
                            <Link :href="route('register')" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold px-8 py-3.5 rounded-lg shadow-lg shadow-emerald-500/20 transition-all text-center">
                                Start Free Trial
                            </Link>
                            <Link :href="route('book-demo')" class="w-full sm:w-auto bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 text-sm font-bold px-8 py-3.5 rounded-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6V4z"/></svg>
                                Book a Demo
                            </Link>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-[11px] font-bold text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                                <span>No Credit Card<br/>Required</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                <span>Setup in<br/>5 Minutes</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                <span>Trusted by<br/>500+ Pharmacies</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Graphic -->
                    <div class="lg:col-span-7 relative">
                        <div class="relative w-full aspect-[4/3] transform rotate-[4deg] hover:rotate-0 transition-transform duration-700 ease-out">
                            <!-- Floating Stats -->
                            <div class="absolute -top-6 left-12 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100 animate-bounce-slow">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">💰</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Today's Sales</div>
                                    <div class="text-sm font-extrabold text-slate-800">$4,285.00 <span class="text-[9px] text-emerald-500 font-bold">+15.3%</span></div>
                                </div>
                            </div>
                            <div class="absolute top-10 right-4 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">⚠️</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Low Stock Items</div>
                                    <div class="text-sm font-extrabold text-orange-500">24 <span class="text-[9px] text-slate-400 font-bold ml-1 cursor-pointer hover:underline">View All</span></div>
                                </div>
                            </div>
                            <div class="absolute top-32 -right-6 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">🛑</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Expiry Alert</div>
                                    <div class="text-sm font-extrabold text-red-500">18 <span class="text-[9px] text-slate-400 font-bold ml-1 cursor-pointer hover:underline">View All</span></div>
                                </div>
                            </div>

                            <!-- Main App Mockup -->
                            <div class="absolute inset-0 bg-white rounded-2xl shadow-2xl shadow-slate-300/60 border border-slate-200 overflow-hidden flex flex-col z-10">
                                <!-- Mockup Header -->
                                <div class="h-10 border-b border-slate-100 flex items-center px-4 justify-between bg-slate-50">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 bg-emerald-500 rounded text-white flex items-center justify-center text-[10px] font-bold">+</div>
                                        <span class="text-xs font-bold text-slate-700">MediSaaS</span>
                                    </div>
                                    <div class="w-64 h-6 bg-white border border-slate-200 rounded text-[9px] text-slate-400 flex items-center px-2 shadow-inner">
                                        🔍 Search barcode or medicine...
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200"></div>
                                        <div class="w-6 h-6 rounded-full bg-emerald-100"></div>
                                    </div>
                                </div>
                                <!-- Mockup Body -->
                                <div class="flex flex-1 overflow-hidden">
                                    <!-- Sidebar -->
                                    <div class="w-16 bg-slate-900 border-r border-slate-800 flex flex-col items-center py-4 gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">🏠</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">📦</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">👥</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">📈</div>
                                    </div>
                                    <!-- Content Area -->
                                    <div class="flex-1 p-4 bg-slate-50 flex gap-4">
                                        <!-- Items List -->
                                        <div class="flex-1 bg-white rounded-lg border border-slate-100 p-3 flex flex-col gap-2">
                                            <div class="flex justify-between items-center pb-2 border-b border-slate-50">
                                                <span class="text-[10px] font-bold text-slate-600">Medicine (Click to Add)</span>
                                                <span class="text-[10px] font-bold text-slate-600">Price</span>
                                            </div>
                                            <div v-for="item in items" :key="item.id" @click="addToCart(item)" class="flex justify-between items-center py-1.5 px-2 rounded hover:bg-slate-50 cursor-pointer transition-colors group">
                                                <div>
                                                    <div class="text-[10px] font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ item.name }}</div>
                                                    <div class="text-[8px] text-slate-400">{{ item.desc }}</div>
                                                </div>
                                                <div class="text-[10px] font-bold text-emerald-600">${{ item.price.toFixed(2) }}</div>
                                            </div>
                                        </div>
                                        <!-- Current Sale -->
                                        <div class="w-48 bg-white rounded-lg border border-slate-100 p-3 flex flex-col">
                                            <div class="text-[11px] font-bold text-slate-800 border-b border-slate-100 pb-2 mb-2 flex justify-between">
                                                <span>Current Sale</span>
                                                <span class="text-slate-400 cursor-pointer hover:text-red-500" @click="cart = []">Clear</span>
                                            </div>
                                            <div class="flex-1 flex flex-col gap-2 overflow-y-auto min-h-[100px]">
                                                <div v-if="cart.length === 0" class="text-[9px] text-slate-400 text-center py-4">Cart is empty</div>
                                                <div v-for="(c, idx) in cart" :key="idx" class="flex justify-between items-center animate-pulse-once">
                                                    <div class="flex items-center gap-1.5">
                                                        <div :class="`w-5 h-5 rounded bg-${c.item.color}-100 text-${c.item.color}-600 flex items-center justify-center text-[8px]`">💊</div>
                                                        <div>
                                                            <div class="text-[9px] font-bold text-slate-700">{{ c.item.name }}</div>
                                                            <div class="text-[7px] text-slate-400">{{ c.qty }} x ${{ c.item.price.toFixed(2) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="text-[9px] font-bold text-slate-700">${{ (c.item.price * c.qty).toFixed(2) }}</div>
                                                </div>
                                            </div>
                                            <div class="pt-2 border-t border-slate-100 mt-2">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] text-slate-500 font-medium">Subtotal</span>
                                                    <span class="text-[10px] font-bold text-slate-700">${{ subtotal.toFixed(2) }}</span>
                                                </div>
                                                <div class="flex justify-between items-center mb-3">
                                                    <span class="text-[10px] text-slate-500 font-medium">Tax</span>
                                                    <span class="text-[10px] font-bold text-slate-700">$0.00</span>
                                                </div>
                                                <div class="flex justify-between items-end mb-3">
                                                    <span class="text-xs font-bold text-slate-800">Total</span>
                                                    <span class="text-lg font-black text-slate-900">${{ subtotal.toFixed(2) }}</span>
                                                </div>
                                                <button class="w-full bg-emerald-500 hover:bg-emerald-600 transition-colors text-white text-[10px] font-bold py-2 rounded-md shadow-sm">Pay (F9)</button>
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

        <!-- Problems We Solve Section -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Problems We Solve</h2>
                    <p class="text-sm text-slate-500 font-medium">We understand the daily challenges of running a pharmacy</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-500 mb-5 border border-red-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Manual & Time Consuming</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Manual billing, stock counting and reports waste time and cause human errors.
                        </p>
                        <div class="text-[10px] font-bold text-red-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> Inefficient</div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 mb-5 border border-orange-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Stock Loss & Expiry</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Untracked expiry and batch management leads to loss of money and trust.
                        </p>
                        <div class="text-[10px] font-bold text-red-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> High Loss</div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 mb-5 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Disconnected Branches</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Multiple branches, different systems and no real-time visibility.
                        </p>
                        <div class="text-[10px] font-bold text-emerald-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> No Control</div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 mb-5 border border-blue-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Complex & Expensive</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Traditional software is costly, complex to use and hard to maintain.
                        </p>
                        <div class="text-[10px] font-bold text-blue-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> High Cost</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose MediSaaS Section -->
        <div class="py-20 bg-slate-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Why Choose MediSaaS?</h2>
                    <p class="text-sm text-slate-500 font-medium">Powerful features to simplify your pharmacy operations</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Smart POS</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Lightning fast billing with barcode scanning, keyboard shortcuts and hold invoices.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Batch & Expiry Tracking</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Track batch-wise stock, get expiry alerts and reduce stock wastage.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Multi-Branch Management</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Manage unlimited branches, users and roles from a single dashboard.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Real-time Reports</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Get real-time insights on sales, profit, stock and business performance.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-teal-700 to-emerald-500 rounded-2xl overflow-hidden shadow-2xl relative flex flex-col md:flex-row items-center">
                    
                    <div class="flex-1 p-10 md:p-14 z-10 relative">
                        <h2 class="text-3xl font-extrabold text-white mb-3">Ready to Transform Your Pharmacy?</h2>
                        <p class="text-emerald-100 font-medium mb-8">Join 500+ pharmacies already growing with MediSaaS.</p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <Link :href="route('register')" class="bg-white text-emerald-700 hover:bg-slate-50 text-sm font-bold px-8 py-3 rounded-lg shadow-lg transition-all w-full sm:w-auto text-center">
                                Start Free Trial
                            </Link>
                            <Link :href="route('book-demo')" class="bg-transparent border border-emerald-300 text-white hover:bg-emerald-600 hover:border-emerald-600 text-sm font-bold px-8 py-3 rounded-lg transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                Book a Demo
                            </Link>
                        </div>
                    </div>

                    <!-- Right Image Area -->
                    <div class="w-full md:w-5/12 h-64 md:h-auto self-stretch relative bg-teal-800">
                        <img src="https://images.unsplash.com/photo-1576602976047-174e57a47881?auto=format&fit=crop&q=80&w=800" alt="Pharmacist" class="absolute inset-0 w-full h-full object-cover opacity-90 object-top" />
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/80 to-transparent md:hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-transparent to-transparent hidden md:block w-32 -ml-1"></div>
                    </div>
                </div>
            </div>
        </div>

    </PublicLayout>
</template>
