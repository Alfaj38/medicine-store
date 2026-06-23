<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    sale: Object,
});

// Auto-print when loaded (optional, but convenient for POS)
onMounted(() => {
    // window.print(); // Uncomment if you want it to pop up print dialog automatically
});

const printReceipt = () => {
    window.print();
};
</script>

<template>
    <Head title="Receipt - SaaSMedi" />

    <div class="min-h-screen bg-slate-100 flex flex-col items-center py-10 print:py-0 print:bg-white">
        
        <!-- Action Buttons (Hidden when printing) -->
        <div class="mb-8 flex gap-4 print:hidden flex-wrap justify-center">
            <Link :href="route('pos.index')" class="px-6 py-3 bg-white text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-colors border border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to POS
            </Link>
            <Link :href="route('sales.index')" class="px-6 py-3 bg-white text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-colors border border-slate-200 flex items-center gap-2">
                Sales History
            </Link>
            <Link :href="route('sale-returns.create', { sale_id: sale.id })" class="px-6 py-3 bg-rose-50 text-rose-700 font-bold rounded-xl shadow-sm hover:bg-rose-100 transition-colors border border-rose-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                Process Return
            </Link>
            <button @click="printReceipt" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-md hover:bg-emerald-500 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Receipt
            </button>
        </div>

        <!-- Receipt Area (Styled for 80mm Thermal Printer) -->
        <div class="bg-white shadow-2xl p-6 print:p-0 print:shadow-none mx-auto text-slate-900 font-mono" style="width: 80mm; max-width: 100%;">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="font-bold text-2xl mb-1 tracking-tight">SaaSMedi</div>
                <div class="text-xs text-slate-600">123 Health Avenue, Medical City</div>
                <div class="text-xs text-slate-600">Phone: +1 234 567 890</div>
                <div class="text-xs text-slate-600 mt-2 font-bold">TAX INVOICE</div>
            </div>

            <!-- Meta Data -->
            <div class="text-xs border-b border-dashed border-slate-300 pb-3 mb-3">
                <div class="flex justify-between">
                    <span>Invoice:</span>
                    <span class="font-bold">{{ sale.invoice_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Date:</span>
                    <span>{{ new Date(sale.created_at).toLocaleString() }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span>Customer:</span>
                    <span class="font-bold">{{ sale.customer ? sale.customer.name : 'Walk-in' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Cashier:</span>
                    <span>{{ sale.user?.name }}</span>
                </div>
            </div>

            <!-- Items -->
            <table class="w-full text-xs mb-3">
                <thead>
                    <tr class="border-b border-dashed border-slate-300 text-left">
                        <th class="py-1">Item</th>
                        <th class="py-1 text-center">Qty</th>
                        <th class="py-1 text-right">Price</th>
                        <th class="py-1 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="border-b border-dashed border-slate-300">
                    <tr v-for="item in sale.items" :key="item.id" class="align-top">
                        <td class="py-2 pr-2">
                            <div class="font-bold leading-tight">{{ item.medicine?.name }}</div>
                            <div class="text-[10px] text-slate-500">B: {{ item.batch_no || 'N/A' }}</div>
                        </td>
                        <td class="py-2 text-center">{{ item.quantity }}</td>
                        <td class="py-2 text-right">${{ item.unit_price }}</td>
                        <td class="py-2 text-right font-bold">${{ item.total }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="text-xs border-b border-dashed border-slate-300 pb-3 mb-3">
                <div class="flex justify-between mb-1">
                    <span>Subtotal:</span>
                    <span>${{ parseFloat(sale.subtotal).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span>Discount:</span>
                    <span>${{ parseFloat(sale.discount).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-sm mt-2 border-t border-slate-200 pt-2">
                    <span>Grand Total:</span>
                    <span>${{ parseFloat(sale.total_amount).toFixed(2) }}</span>
                </div>
            </div>

            <!-- Payments -->
            <div class="text-xs border-b border-dashed border-slate-300 pb-3 mb-3">
                <div class="flex justify-between mb-1">
                    <span>Paid ({{ sale.payment_method.toUpperCase() }}):</span>
                    <span class="font-bold">${{ parseFloat(sale.paid_amount).toFixed(2) }}</span>
                </div>
                <div v-if="sale.paid_amount > sale.total_amount" class="flex justify-between font-bold">
                    <span>Change:</span>
                    <span>${{ (sale.paid_amount - sale.total_amount).toFixed(2) }}</span>
                </div>
                <div v-else-if="sale.paid_amount < sale.total_amount" class="flex justify-between font-bold text-slate-900">
                    <span>Due Balance:</span>
                    <span>${{ (sale.total_amount - sale.paid_amount).toFixed(2) }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs mt-6">
                <div class="font-bold mb-1">Thank you for your visit!</div>
                <div class="text-[10px] text-slate-500">Wishing you a quick recovery.</div>
                <div class="mt-4 text-[10px] text-slate-400">Software by SaaSMedi</div>
            </div>
            
            <!-- Barcode Placeholder (Optional) -->
            <div class="mt-4 flex justify-center opacity-50">
                <div class="h-10 w-48 bg-slate-200 flex items-center justify-center text-[10px] tracking-[0.3em]">
                    |||||||||||||||||||||||
                </div>
            </div>

        </div>
    </div>

    <style>
        /* CSS to hide non-receipt elements during printing */
        @media print {
            body { background: white; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</template>
