<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TopNavbar from '@/Components/TopNavbar.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    order: Object,
});

const form = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
});

const updateStatus = () => {
    form.put(route('online-orders.update', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Order status has been successfully updated.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    accepted: 'bg-blue-100 text-blue-800',
    processing: 'bg-purple-100 text-purple-800',
    out_for_delivery: 'bg-orange-100 text-orange-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const formatStatus = (status) => {
    return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const printPackingSlip = () => {
    window.print();
};
</script>

<template>
    <Head :title="`Order ${order.tracking_number} - MediSaaS`" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <TopNavbar class="print:hidden" />

        <main class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8 print:py-0 print:px-0">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between mb-8 print:hidden">
                <div class="flex items-center gap-4">
                    <Link :href="route('online-orders.index')" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 shadow-sm transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                            Order {{ order.tracking_number }}
                            <span :class="[statusColors[order.status], 'text-xs px-2 py-1 rounded-md uppercase tracking-wider font-bold']">{{ formatStatus(order.status) }}</span>
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">Placed on {{ new Date(order.created_at).toLocaleString() }}</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-3">
                    <button @click="printPackingSlip" class="inline-flex items-center justify-center rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Packing Slip
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Print Header -->
                    <div class="hidden print:block text-center border-b border-slate-300 pb-4 mb-6 mt-4">
                        <h2 class="text-xl font-bold uppercase tracking-widest border border-slate-300 inline-block px-4 py-1 rounded mb-4">PACKING SLIP</h2>
                        <div class="flex justify-between items-end text-sm">
                            <div class="text-left">
                                <p class="font-bold text-slate-800">Order ID: {{ order.tracking_number }}</p>
                                <p class="text-slate-600">Date: {{ new Date(order.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800">Payment: <span class="uppercase">{{ order.payment_method }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print:shadow-none print:border-none print:rounded-none">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 print:bg-transparent print:px-0 print:py-2">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <span class="print:hidden">📦</span> Order Items
                            </h3>
                        </div>
                        <ul class="divide-y divide-slate-200 print:divide-slate-300">
                            <li v-for="item in order.items" :key="item.id" class="px-6 py-4 flex items-center justify-between print:px-0 print:py-2">
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ item.item_name }}</p>
                                    <p class="text-sm text-slate-500 mt-1">Price: ৳{{ item.unit_price }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-slate-900 text-lg print:text-base">× {{ item.quantity }}</p>
                                    <p class="text-sm font-bold text-emerald-600 mt-1 print:text-slate-900">৳{{ item.total_price }}</p>
                                </div>
                            </li>
                        </ul>
                        <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 print:bg-transparent print:px-0 print:pt-4">
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-slate-500 font-medium">Subtotal</dt>
                                    <dd class="text-slate-900 font-bold">৳{{ order.subtotal }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500 font-medium">Delivery Fee</dt>
                                    <dd class="text-slate-900 font-bold">৳{{ order.delivery_fee }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-2 mt-2">
                                    <dt class="text-slate-900 font-bold text-base">Total Amount</dt>
                                    <dd class="text-emerald-600 font-black text-xl print:text-slate-900">৳{{ order.total_amount }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Customer Info & Status Management -->
                <div class="space-y-8">
                    <!-- Status Management Card -->
                    <form @submit.prevent="updateStatus" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print:hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                ⚙️ Manage Status
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Order Status</label>
                                <select v-model="form.status" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="processing">Processing</option>
                                    <option value="out_for_delivery">Out for Delivery</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Payment Status</label>
                                <select v-model="form.payment_status" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                {{ form.processing ? 'Saving...' : 'Update Order' }}
                            </button>
                        </div>
                    </form>

                    <!-- Customer Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print:shadow-none print:border-none print:rounded-none">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 print:bg-transparent print:px-0 print:border-b-2 print:border-slate-800">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <span class="print:hidden">👤</span> Customer Details
                            </h3>
                        </div>
                        <div class="p-6 space-y-4 print:px-0 print:py-4">
                            <div>
                                <p class="text-sm text-slate-500 mb-1 font-medium">Name</p>
                                <p class="text-slate-900 font-bold">{{ order.customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 mb-1 font-medium">Phone Number</p>
                                <p class="text-slate-900 font-bold">{{ order.customer_phone }}</p>
                            </div>
                            <div v-if="order.delivery_method === 'home'">
                                <p class="text-sm text-slate-500 mb-1 font-medium">Delivery Address</p>
                                <p class="text-slate-900 font-medium whitespace-pre-line">{{ order.delivery_address }}</p>
                            </div>
                            <div v-else>
                                <p class="text-sm text-slate-500 mb-1 font-medium">Delivery Method</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 print:bg-transparent print:px-0">
                                    Store Pickup
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription -->
                    <div v-if="order.prescription_path" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print:hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                📄 Prescription
                            </h3>
                        </div>
                        <div class="p-6">
                            <a :href="'/storage/' + order.prescription_path" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 px-4 border-2 border-emerald-500 rounded-xl shadow-sm text-sm font-bold text-emerald-700 hover:bg-emerald-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View Attached Prescription
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@media print {
    @page { margin: 0.5cm; }
    body { background-color: white !important; }
}
</style>
