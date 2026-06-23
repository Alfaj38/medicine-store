<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, computed } from 'vue';

const activeFeature = ref(1);

// POS state
const items = ref([
    { id: 1, name: 'Napa Extra', desc: 'Paracetamol 500mg', price: 2.50, color: 'emerald' },
    { id: 2, name: 'Seclo 20mg', desc: 'Omeprazole', price: 5.00, color: 'blue' },
    { id: 3, name: 'Alatrol', desc: 'Cetirizine 10mg', price: 1.50, color: 'orange' },
]);
const cart = ref([{ item: items.value[0], qty: 2 }]);
const addToCart = (item) => {
    const existing = cart.value.find(c => c.item.id === item.id);
    if (existing) existing.qty++;
    else cart.value.push({ item, qty: 1 });
};
const subtotal = computed(() => cart.value.reduce((acc, c) => acc + (c.item.price * c.qty), 0));

// Inventory state
const inventory = [
    { name: 'Napa Extra', stock: 1540, expiry: '2027-12-01', status: 'Good' },
    { name: 'Seclo 20mg', stock: 45, expiry: '2026-08-15', status: 'Low' },
    { name: 'Alatrol', stock: 8, expiry: '2026-07-10', status: 'Critical' },
];

</script>

<template>
    <Head title="Features - SaaSMedi" />
    <PublicLayout>
        <!-- Header -->
        <div class="bg-slate-50/50 pt-32 pb-16 text-center px-4 sm:px-6 lg:px-8">
            <div class="text-emerald-600 font-bold text-sm tracking-widest uppercase mb-4">Features</div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 max-w-3xl mx-auto tracking-tight">Everything You Need to <br/><span class="text-emerald-500">Run Your Pharmacy Smarter</span></h1>
            <p class="text-[15px] font-medium text-slate-600 max-w-2xl mx-auto">Powerful modules built specifically for the modern pharmacy business.</p>
        </div>

        <!-- Main Features Split Layout -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 bg-slate-50/50">
            <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-start">
                
                <!-- Feature List -->
                <div class="lg:col-span-5 space-y-4">
                    <div @click="activeFeature = 1" :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer', activeFeature === 1 ? 'bg-white border-emerald-500 shadow-xl shadow-emerald-500/10' : 'bg-white border-slate-100 hover:border-emerald-300 opacity-60 hover:opacity-100']">
                        <div class="flex items-start gap-4">
                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg', activeFeature === 1 ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500']">💳</div>
                            <div>
                                <h3 class="text-[15px] font-bold text-slate-900 mb-1">Smart Point of Sale (POS)</h3>
                                <p class="text-slate-500 text-[12px] leading-relaxed">Fast billing with barcode support, keyboard shortcuts, and multiple payment options in one screen.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div @click="activeFeature = 2" :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer', activeFeature === 2 ? 'bg-white border-blue-500 shadow-xl shadow-blue-500/10' : 'bg-white border-slate-100 hover:border-blue-300 opacity-60 hover:opacity-100']">
                        <div class="flex items-start gap-4">
                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg', activeFeature === 2 ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500']">📦</div>
                            <div>
                                <h3 class="text-[15px] font-bold text-slate-900 mb-1">Inventory & Ledger</h3>
                                <p class="text-slate-500 text-[12px] leading-relaxed">Batch tracking, automated expiry alerts, low stock warnings, and real-time stock ledgers.</p>
                            </div>
                        </div>
                    </div>

                    <div @click="activeFeature = 3" :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer', activeFeature === 3 ? 'bg-white border-indigo-500 shadow-xl shadow-indigo-500/10' : 'bg-white border-slate-100 hover:border-indigo-300 opacity-60 hover:opacity-100']">
                        <div class="flex items-start gap-4">
                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg', activeFeature === 3 ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500']">🏢</div>
                            <div>
                                <h3 class="text-[15px] font-bold text-slate-900 mb-1">Multi-Branch & Users</h3>
                                <p class="text-slate-500 text-[12px] leading-relaxed">Centralized management. Assign roles (Cashier, Manager) and control exact permissions per user.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visuals -->
                <div class="lg:col-span-7 mt-12 lg:mt-0 sticky top-32">
                    <div class="relative rounded-2xl bg-slate-50 border border-slate-200 shadow-2xl overflow-hidden pt-6 px-6 aspect-[4/3] flex flex-col transition-all duration-500">
                        <div class="absolute top-3 left-4 flex gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div></div>
                        
                        <!-- Interactive POS Mockup -->
                        <div v-if="activeFeature === 1" class="flex-1 bg-white rounded-t-xl border border-slate-200 border-b-0 shadow-lg overflow-hidden flex flex-col animate-fade-in">
                            <div class="h-10 border-b border-slate-100 flex items-center px-4 bg-slate-50">
                                <span class="text-xs font-bold text-slate-700">Point of Sale</span>
                            </div>
                            <div class="flex-1 p-4 bg-slate-50 flex gap-4">
                                <div class="flex-1 bg-white rounded-lg border border-slate-100 p-3 flex flex-col">
                                    <div class="text-[10px] font-bold text-slate-600 border-b border-slate-50 pb-2 mb-2">Medicine (Click to Add)</div>
                                    <div v-for="item in items" :key="item.id" @click="addToCart(item)" class="flex justify-between items-center py-2 px-2 rounded hover:bg-slate-50 cursor-pointer group">
                                        <div>
                                            <div class="text-[11px] font-bold text-slate-800 group-hover:text-emerald-600">{{ item.name }}</div>
                                            <div class="text-[9px] text-slate-400">{{ item.desc }}</div>
                                        </div>
                                        <div class="text-[10px] font-bold text-emerald-600">${{ item.price.toFixed(2) }}</div>
                                    </div>
                                </div>
                                <div class="w-48 bg-white rounded-lg border border-slate-100 p-3 flex flex-col">
                                    <div class="text-[11px] font-bold text-slate-800 border-b border-slate-100 pb-2 mb-2 flex justify-between">
                                        <span>Cart</span>
                                        <span class="text-slate-400 cursor-pointer hover:text-red-500" @click="cart = []">Clear</span>
                                    </div>
                                    <div class="flex-1 flex flex-col gap-2 overflow-y-auto">
                                        <div v-for="(c, idx) in cart" :key="idx" class="flex justify-between items-center">
                                            <div class="text-[9px] font-bold text-slate-700">{{ c.qty }}x {{ c.item.name }}</div>
                                            <div class="text-[9px] font-bold text-slate-700">${{ (c.item.price * c.qty).toFixed(2) }}</div>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 mt-2">
                                        <div class="flex justify-between items-end mb-3">
                                            <span class="text-xs font-bold text-slate-800">Total</span>
                                            <span class="text-lg font-black text-emerald-600">${{ subtotal.toFixed(2) }}</span>
                                        </div>
                                        <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold py-2 rounded-md">Pay (F9)</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Mockup -->
                        <div v-else-if="activeFeature === 2" class="flex-1 bg-white rounded-t-xl border border-slate-200 border-b-0 shadow-lg overflow-hidden flex flex-col animate-fade-in">
                            <div class="h-10 border-b border-slate-100 flex items-center px-4 bg-slate-50 justify-between">
                                <span class="text-xs font-bold text-slate-700">Inventory Management</span>
                                <div class="px-2 py-1 bg-orange-100 text-orange-600 text-[9px] font-bold rounded">2 Low Stock</div>
                            </div>
                            <div class="flex-1 p-4 bg-slate-50">
                                <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
                                    <table class="w-full text-left text-[10px]">
                                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                                            <tr>
                                                <th class="px-4 py-2 font-bold">Item Name</th>
                                                <th class="px-4 py-2 font-bold">Stock</th>
                                                <th class="px-4 py-2 font-bold">Expiry</th>
                                                <th class="px-4 py-2 font-bold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="inv in inventory" :key="inv.name" class="border-b border-slate-50 last:border-0">
                                                <td class="px-4 py-3 font-bold text-slate-800">{{ inv.name }}</td>
                                                <td class="px-4 py-3 font-medium text-slate-600">{{ inv.stock }}</td>
                                                <td class="px-4 py-3 font-medium text-slate-600">{{ inv.expiry }}</td>
                                                <td class="px-4 py-3">
                                                    <span v-if="inv.status === 'Good'" class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold">Good</span>
                                                    <span v-else-if="inv.status === 'Low'" class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-[9px] font-bold">Low</span>
                                                    <span v-else class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold">Critical</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Branch Mockup -->
                        <div v-else class="flex-1 bg-white rounded-t-xl border border-slate-200 border-b-0 shadow-lg overflow-hidden flex flex-col animate-fade-in">
                            <div class="h-10 border-b border-slate-100 flex items-center px-4 bg-slate-50">
                                <span class="text-xs font-bold text-slate-700">Branch Network</span>
                            </div>
                            <div class="flex-1 p-6 bg-slate-50 flex items-center justify-center">
                                <div class="flex items-center gap-4 w-full">
                                    <div class="flex-1 bg-white p-4 rounded-xl border border-slate-200 text-center shadow-sm relative">
                                        <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-0.5 bg-indigo-200"></div>
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mx-auto mb-2 text-xs">🏢</div>
                                        <div class="text-[10px] font-bold text-slate-800">HQ Pharmacy</div>
                                        <div class="text-[8px] text-slate-400">Super Admin</div>
                                    </div>
                                    <div class="flex flex-col gap-3 flex-1">
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                            <div>
                                                <div class="text-[9px] font-bold text-slate-800">Branch North</div>
                                                <div class="text-[8px] text-emerald-600 font-bold">Online</div>
                                            </div>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                            <div>
                                                <div class="text-[9px] font-bold text-slate-800">Branch South</div>
                                                <div class="text-[8px] text-emerald-600 font-bold">Online</div>
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

        <!-- Infrastructure icons -->
        <div class="bg-white py-16 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 mx-auto text-emerald-500 mb-3 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg></div>
                        <h4 class="font-bold text-slate-900 text-sm">Cloud Based</h4>
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">Access anywhere, anytime</p>
                    </div>
                    <div class="text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 mx-auto text-emerald-500 mb-3 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <h4 class="font-bold text-slate-900 text-sm">Secure & Reliable</h4>
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">99.9% uptime guarantee</p>
                    </div>
                    <div class="text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 mx-auto text-emerald-500 mb-3 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></div>
                        <h4 class="font-bold text-slate-900 text-sm">Automatic Backups</h4>
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">Your data is always safe</p>
                    </div>
                    <div class="text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 mx-auto text-emerald-500 mb-3 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                        <h4 class="font-bold text-slate-900 text-sm">Regular Updates</h4>
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">New features automatically</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
