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

const shareWhatsApp = () => {
    const text = `*SaaSMedi Receipt*\nInvoice: ${props.sale.invoice_no}\nTotal: $${parseFloat(props.sale.total_amount).toFixed(2)}\nThank you for your visit!`;
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Receipt - SaaSMedi" />

    <div class="min-h-screen bg-slate-100 flex flex-col items-center pb-24 sm:pb-10 pt-4 sm:pt-10 print:py-0 print:bg-white print:pb-0">
        
        <!-- Desktop Action Buttons (Hidden on mobile and printing) -->
        <div class="hidden sm:flex mb-8 gap-4 print:hidden flex-wrap justify-center w-full max-w-2xl px-4">
            <Link :href="route('pos.index')" class="px-6 py-3 bg-white text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-colors border border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                New Sale
            </Link>
            <Link :href="route('sales.index')" class="px-6 py-3 bg-white text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-colors border border-slate-200 flex items-center gap-2">
                Sales History
            </Link>
            <button @click="shareWhatsApp" class="px-6 py-3 bg-green-50 text-green-700 font-bold rounded-xl shadow-sm hover:bg-green-100 transition-colors border border-green-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                WhatsApp
            </button>
            <button @click="printReceipt" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-md hover:bg-emerald-500 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
        </div>

        <!-- Receipt Area (Styled for 80mm Thermal Printer) -->
        <div class="bg-white shadow-2xl sm:shadow-2xl p-6 print:p-0 print:shadow-none mx-auto text-slate-900 font-mono w-full sm:w-[80mm] max-w-[80mm]" id="receipt-content">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="font-bold text-2xl mb-1 tracking-tight">SaaSMedi</div>
                <div class="text-xs text-slate-600">123 Health Avenue, Medical City</div>
                <div class="text-xs text-slate-600">Phone: +1 234 567 890</div>
                <div class="text-xs text-slate-600 mt-2 font-bold">TAX INVOICE</div>
            </div>

            <!-- Meta Data -->
            <div class="text-[11px] border-b border-dashed border-slate-300 pb-3 mb-3">
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
            <table class="w-full text-[11px] mb-3">
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
                            <div class="font-bold leading-tight">{{ item.medicine?.name || 'Item' }}</div>
                            <div class="text-[9px] text-slate-500">B: {{ item.batch_no || 'N/A' }}</div>
                        </td>
                        <td class="py-2 text-center">{{ item.quantity }}</td>
                        <td class="py-2 text-right">${{ item.unit_price }}</td>
                        <td class="py-2 text-right font-bold">${{ item.total }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="text-[11px] border-b border-dashed border-slate-300 pb-3 mb-3">
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
            <div class="text-[11px] border-b border-dashed border-slate-300 pb-3 mb-3">
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
            <div class="text-center text-[10px] mt-6">
                <div class="font-bold mb-1">Thank you for your visit!</div>
                <div class="text-[9px] text-slate-500">Wishing you a quick recovery.</div>
                <div class="mt-4 text-[9px] text-slate-400">Software by SaaSMedi</div>
            </div>
            
            <!-- Barcode Placeholder (Optional) -->
            <div class="mt-4 flex justify-center opacity-50">
                <div class="h-10 w-48 bg-slate-200 flex items-center justify-center text-[10px] tracking-[0.3em]">
                    |||||||||||||||||||||||
                </div>
            </div>

        </div>

        <!-- Sticky Mobile Action Bar -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-3 flex gap-2 sm:hidden print:hidden shadow-[0_-4px_10px_rgba(0,0,0,0.05)] z-50">
            <button @click="printReceipt" class="flex-1 bg-slate-100 text-slate-700 font-bold py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-slate-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            </button>
            <button @click="shareWhatsApp" class="flex-1 bg-green-50 text-green-600 border border-green-200 font-bold py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-green-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
            </button>
            <Link :href="route('pos.index')" class="flex-[2] bg-emerald-600 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-emerald-500 transition-colors shadow-sm">
                New Sale
            </Link>
        </div>
    </div>
</template>

<style>
    /* CSS to hide non-receipt elements during printing */
    @media print {
        body { background: white; }
        @page { margin: 0; size: 80mm auto; }
    }
</style>
