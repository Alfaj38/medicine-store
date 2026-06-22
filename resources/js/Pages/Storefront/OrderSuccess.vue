<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    company: Object,
    order: Object,
});

const printInvoice = () => {
    window.print();
};

const statuses = [
    { id: 'pending', label: 'Order Placed', icon: '📝' },
    { id: 'accepted', label: 'Accepted', icon: '✅' },
    { id: 'processing', label: 'Processing', icon: '⚙️' },
    { id: 'out_for_delivery', label: 'Out for Delivery', icon: '🚚' },
    { id: 'delivered', label: 'Delivered', icon: '🎉' },
];

const getCurrentStatusIndex = () => {
    const idx = statuses.findIndex(s => s.id === props.order.status);
    return idx === -1 ? 0 : idx;
};

const currentStatusIndex = getCurrentStatusIndex();
</script>

<template>
    <Head :title="`Order Tracking - ${company.name}`" />

    <StorefrontLayout :company="company" hide-cart>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 print:p-0 print:max-w-none">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden print:border-none print:shadow-none print:rounded-none">
                <!-- Header Area (Screen Only) -->
                <div class="bg-emerald-50/50 border-b border-slate-200 p-8 text-center relative overflow-hidden print:hidden">
                    <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0ibm9uZSI+PC9yZWN0Pgo8Y2lyY2xlIGN4PSIyIiBjeT0iMiIgcj0iMSIgZmlsbD0iIzAwYTY2OSI+PC9jaXJjbGU+Cjwvc3ZnPg==')]"></div>
                    
                    <div class="w-20 h-20 bg-white shadow-md rounded-full flex items-center justify-center mx-auto mb-4 relative z-10">
                        <span class="text-4xl">🎉</span>
                    </div>
                    
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-2 relative z-10">Order Confirmed!</h1>
                    <p class="text-slate-600 relative z-10">Thank you, <span class="font-bold text-slate-800">{{ order.customer_name }}</span>. Your order has been received.</p>
                    
                    <div class="mt-6 flex flex-wrap justify-center gap-4 relative z-10 print:hidden">
                        <button @click="printInvoice" class="bg-white border border-slate-200 hover:border-emerald-300 text-slate-700 font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Receipt
                        </button>
                        <Link :href="route('storefront.search', company.slug)" class="bg-[#00a669] hover:bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm shadow-emerald-500/20">
                            Continue Shopping
                        </Link>
                    </div>
                </div>

                <!-- Print Header (Print Only) -->
                <div class="hidden print:block border-b border-slate-300 pb-4 mb-4 mt-0 relative">
                    <div class="absolute top-0 right-0 text-right">
                        <p class="text-[10px] text-slate-500 leading-tight">Generated: {{ new Date().toLocaleString() }}</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-slate-900 uppercase tracking-wide leading-none mb-2">{{ company.name }}</h1>
                        <p class="text-sm text-slate-600 leading-tight mb-1">{{ company.address }}</p>
                        <p class="text-sm text-slate-600 leading-tight">Phone: {{ company.phone }}</p>
                        <h2 class="text-lg font-black text-slate-800 mt-3 border border-slate-300 inline-block px-4 py-1 rounded">ORDER RECEIPT</h2>
                    </div>
                </div>

                <!-- Tracking Timeline (Hidden in Print) -->
                <div class="p-8 border-b border-slate-100 bg-slate-50 print:hidden">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <span>📍</span> Order Status
                    </h2>
                    
                    <div class="relative">
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-200">
                            <div :style="`width: ${Math.max(10, (currentStatusIndex / (statuses.length - 1)) * 100)}%`" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#00a669] transition-all duration-500"></div>
                        </div>
                        <div class="flex justify-between w-full">
                            <div v-for="(status, index) in statuses" :key="status.id" class="flex flex-col items-center">
                                <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm mb-1 transition-all', index <= currentStatusIndex ? 'bg-[#00a669] text-white shadow-md shadow-emerald-500/30' : 'bg-white border-2 border-slate-200 text-slate-400']">
                                    {{ status.icon }}
                                </div>
                                <span :class="['text-xs font-bold text-center w-16 leading-tight', index <= currentStatusIndex ? 'text-slate-800' : 'text-slate-400']">
                                    {{ status.label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 print:p-0 print:gap-4 print:grid-cols-2 print:mt-2">
                    <!-- Order Details -->
                    <div class="print:text-sm">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100 print:text-base print:mb-2 print:pb-1">
                            <span class="print:hidden">🧾</span> Order Details
                        </h2>
                        
                        <div class="space-y-3 print:space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500 text-sm print:text-xs">Order ID</span>
                                <span class="font-bold text-slate-800">{{ order.tracking_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 text-sm print:text-xs">Date</span>
                                <span class="font-medium text-slate-800">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 text-sm print:text-xs">Payment Method</span>
                                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase text-xs print:bg-transparent print:px-0 print:text-slate-800">
                                    {{ order.payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}
                                </span>
                            </div>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900 mt-8 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100 print:text-base print:mt-4 print:mb-2 print:pb-1">
                            <span class="print:hidden">🚚</span> Delivery Info
                        </h2>
                        
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm print:bg-transparent print:border-none print:p-0">
                            <p class="font-bold text-slate-800 mb-1">{{ order.customer_name }}</p>
                            <p class="text-slate-600 mb-1">{{ order.customer_phone }}</p>
                            <p class="text-slate-600">
                                <span v-if="order.delivery_method === 'home'">
                                    {{ order.delivery_address }}<br>
                                    {{ order.city }}
                                </span>
                                <span v-else class="font-bold text-emerald-600">
                                    Store Pickup from {{ company.name }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="print:text-sm">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100 print:text-base print:mb-2 print:pb-1">
                            <span class="print:hidden">💊</span> Items Ordered
                        </h2>
                        
                        <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200 print:max-h-none print:overflow-visible print:space-y-1 print:mb-4 print:pr-0">
                            <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 print:p-1.5 print:bg-transparent print:border-0 print:border-b print:border-slate-200 print:rounded-none">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm leading-tight print:text-xs">{{ item.item_name }}</p>
                                    <p class="text-xs text-slate-500">Qty: {{ item.quantity }} × ৳{{ item.unit_price }}</p>
                                </div>
                                <div class="font-bold text-slate-800 print:text-sm">
                                    ৳{{ item.total_price }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 print:bg-transparent print:border-none print:p-0 print:ml-auto print:w-48">
                            <div class="flex justify-between items-center mb-2 print:mb-1">
                                <span class="text-sm text-slate-600 print:text-xs">Subtotal</span>
                                <span class="text-sm font-bold text-slate-800 print:text-xs">৳{{ order.subtotal }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-3 pb-3 border-b border-dashed border-slate-300 print:mb-1 print:pb-1">
                                <span class="text-sm text-slate-600 print:text-xs">Delivery Fee</span>
                                <span class="text-sm font-bold text-slate-800 print:text-xs">৳{{ order.delivery_fee }}</span>
                            </div>
                            <div class="flex justify-between items-end print:mt-1">
                                <span class="font-bold text-slate-900 print:text-sm">Total</span>
                                <span class="text-2xl font-black text-[#00a669] print:text-lg print:text-slate-900">৳{{ order.total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </StorefrontLayout>
</template>

<style>
@media print {
    @page {
        margin: 0.5cm;
    }
    body {
        background-color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    nav {
        display: none !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
}
</style>
