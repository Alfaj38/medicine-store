<script setup>
import TopNavbar from '@/Components/TopNavbar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    purchase: Object,
    availableCredit: Number,
});

const isPaymentModalOpen = ref(false);
const pendingCreditNotes = ref([]);
const isLoadingCreditNotes = ref(false);

const paymentForm = useForm({
    amount: '',
    payment_method: 'cash',
    payment_date: new Date().toISOString().slice(0,10),
    transaction_id: '',
    notes: '',
    credit_note_ids: [],
});

const totalSelectedCredit = computed(() => {
    if (!pendingCreditNotes.value.length) return 0;
    return pendingCreditNotes.value
        .filter(cn => paymentForm.credit_note_ids.includes(cn.id))
        .reduce((sum, cn) => sum + parseFloat(cn.amount), 0);
});

watch(() => paymentForm.credit_note_ids, () => {
    const due = props.purchase.total_amount - props.purchase.paid_amount;
    let cashDue = due - totalSelectedCredit.value;
    if (cashDue < 0) cashDue = 0;
    paymentForm.amount = cashDue.toFixed(2);
}, { deep: true });

const openPaymentModal = async () => {
    const due = props.purchase.total_amount - props.purchase.paid_amount;
    paymentForm.amount = due.toFixed(2);
    paymentForm.payment_method = 'cash';
    paymentForm.payment_date = new Date().toISOString().slice(0,10);
    paymentForm.transaction_id = '';
    paymentForm.notes = '';
    paymentForm.credit_note_ids = [];
    isPaymentModalOpen.value = true;
    pendingCreditNotes.value = [];
    
    isLoadingCreditNotes.value = true;
    try {
        const response = await fetch(route('purchases.pending-credit-notes', props.purchase.supplier_id));
        pendingCreditNotes.value = await response.json();
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingCreditNotes.value = false;
    }
};

const closePaymentModal = () => {
    isPaymentModalOpen.value = false;
    paymentForm.reset();
    paymentForm.clearErrors();
};

const submitPayment = () => {
    paymentForm.post(route('purchases.pay', props.purchase.id), {
        preserveScroll: true,
        onSuccess: () => {
            closePaymentModal();
        },
    });
};
</script>

<template>
    <Head :title="'Purchase ' + purchase.reference_no" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-20">
        <TopNavbar />

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Credit Note Alert -->
            <div v-if="availableCredit > 0" class="mb-6 flex items-start gap-3 bg-indigo-50 border border-indigo-200 rounded-2xl px-5 py-4">
                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-indigo-900">Supplier Credit Available</p>
                    <p class="text-sm text-indigo-700 mt-0.5">This supplier has <span class="font-bold">${{ availableCredit.toFixed(2) }}</span> in pending Credit Notes from returns. Use the <strong>Record Payment</strong> button to apply them against this invoice.</p>
                </div>
                <Link :href="route('purchase-returns.create')" class="flex-shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition-colors">
                    New Return
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </Link>
            </div>
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between gap-6">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Reference No.</p>
                        <h2 class="text-2xl font-bold text-slate-900">{{ purchase.reference_no }}</h2>
                        <div class="mt-4 space-y-1 text-sm text-slate-600">
                            <p><span class="font-medium text-slate-800">Purchase Date:</span> {{ new Date(purchase.purchase_date).toLocaleDateString() }}</p>
                            <p><span class="font-medium text-slate-800">Status:</span> 
                                <span class="capitalize">{{ purchase.status }}</span>
                            </p>
                            <p><span class="font-medium text-slate-800">Payment Status:</span>
                                <span v-if="purchase.payment_status === 'paid'" class="ml-2 inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Paid</span>
                                <span v-else-if="purchase.payment_status === 'partial'" class="ml-2 inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Partial</span>
                                <span v-else class="ml-2 inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">Unpaid</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm font-medium text-slate-500 mb-1">Supplier Details</p>
                        <h3 class="text-lg font-bold text-slate-900">{{ purchase.supplier?.name }}</h3>
                        <p class="text-sm text-slate-600">{{ purchase.supplier?.companies && purchase.supplier.companies.length ? purchase.supplier.companies.join(', ') : purchase.supplier?.company_name || 'Individual' }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ purchase.supplier?.phone }}</p>
                        <p class="text-sm text-slate-500">{{ purchase.supplier?.email }}</p>
                        <p class="text-sm text-slate-500 whitespace-pre-wrap mt-1 max-w-xs ml-auto">{{ purchase.supplier?.address }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Item</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Batch & Expiry</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in purchase.items" :key="item.id" class="hover:bg-slate-50/50">
                                <td class="py-4 pl-6 pr-3 text-sm">
                                    <div class="font-medium text-slate-900">{{ item.medicine?.name }}</div>
                                    <div class="text-slate-500 text-xs">{{ item.medicine?.generic_name }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-600">
                                    <div v-if="item.batch_no">Batch: <span class="font-medium text-slate-800">{{ item.batch_no }}</span></div>
                                    <div v-else class="text-slate-400">No Batch</div>
                                    <div v-if="item.expiry_date">Exp: <span class="font-medium text-slate-800">{{ item.expiry_date }}</span></div>
                                </td>
                                <td class="px-3 py-4 text-sm text-right">
                                    <div class="font-medium text-slate-900">{{ item.quantity }}</div>
                                    <div v-if="item.returned_quantity > 0" class="mt-1 inline-flex items-center gap-1 rounded-md bg-purple-50 px-1.5 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-600/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/></svg>
                                        {{ item.returned_quantity }} Returned
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-right text-slate-600">${{ parseFloat(item.unit_price).toFixed(2) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-slate-900">${{ parseFloat(item.subtotal).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-50 border-t border-slate-200 p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between gap-8">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-slate-900 mb-2">Purchase Notes</h4>
                            <p class="text-sm text-slate-600 whitespace-pre-wrap bg-white p-4 rounded-xl border border-slate-200 min-h-[80px]">{{ purchase.notes || 'No additional notes provided.' }}</p>
                        </div>
                        <div class="w-full sm:w-72 space-y-3">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-slate-900">${{ parseFloat(purchase.subtotal).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Discount</span>
                                <span class="font-medium text-rose-600">-${{ parseFloat(purchase.discount).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Tax</span>
                                <span class="font-medium text-slate-900">+${{ parseFloat(purchase.tax).toFixed(2) }}</span>
                            </div>
                            <div class="pt-3 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-base font-bold text-slate-900">Grand Total</span>
                                <span class="text-lg font-bold text-emerald-600">${{ parseFloat(purchase.total_amount).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600 mt-2">
                                <span>Paid Amount</span>
                                <span class="font-medium text-slate-900">${{ parseFloat(purchase.paid_amount).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm bg-rose-50 text-rose-700 p-2 rounded-lg border border-rose-100 mt-2">
                                <span class="font-semibold">Balance Due</span>
                                <span class="font-bold">${{ (purchase.total_amount - purchase.paid_amount).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div v-if="purchase.payments && purchase.payments.length > 0" class="border-t border-slate-200">
                    <div class="p-6 sm:p-8 bg-white">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Payment History</h3>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Method</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaction ID</th>
                                        <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="payment in purchase.payments" :key="payment.id" class="hover:bg-slate-50">
                                        <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-slate-900">{{ new Date(payment.payment_date).toLocaleDateString() }}</td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-500 capitalize">{{ payment.payment_method.replace('_', ' ') }}</td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-500">{{ payment.transaction_id || '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-right font-bold text-emerald-600">${{ parseFloat(payment.amount).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Payment Modal -->
        <div v-if="isPaymentModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                        <form @submit.prevent="submitPayment">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title">Record Payment</h3>
                                        <div class="mt-2 text-sm text-slate-500">
                                            <p>Paying supplier <span class="font-bold text-slate-700">{{ purchase?.supplier?.name }}</span> for PO <span class="font-bold text-slate-700">{{ purchase?.reference_no }}</span>.</p>
                                        </div>

                                        <div v-if="isLoadingCreditNotes" class="mt-4 text-sm text-indigo-600 animate-pulse">
                                            Checking for available Credit Notes...
                                        </div>

                                        <div v-else-if="pendingCreditNotes.length > 0" class="mt-4 bg-indigo-50/50 border border-indigo-100 rounded-xl p-4">
                                            <h4 class="text-sm font-semibold text-indigo-900 mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                                                Apply Credit Notes
                                            </h4>
                                            <div class="space-y-2 max-h-32 overflow-y-auto">
                                                <label v-for="cn in pendingCreditNotes" :key="cn.id" class="flex items-center justify-between p-2 rounded-lg bg-white border border-indigo-100 hover:border-indigo-300 cursor-pointer transition-colors">
                                                    <div class="flex items-center gap-3">
                                                        <input type="checkbox" :value="cn.id" v-model="paymentForm.credit_note_ids" class="rounded text-indigo-600 focus:ring-indigo-500 border-indigo-300">
                                                        <span class="text-sm font-medium text-slate-700">{{ cn.credit_note_no }}</span>
                                                    </div>
                                                    <span class="text-sm font-bold text-indigo-700">${{ parseFloat(cn.amount).toFixed(2) }}</span>
                                                </label>
                                            </div>
                                            <div v-if="totalSelectedCredit > 0" class="mt-3 text-right text-sm">
                                                Total Credit Applied: <span class="font-bold text-indigo-700">-${{ totalSelectedCredit.toFixed(2) }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-5 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">Amount ($)</label>
                                                <input v-model="paymentForm.amount" type="number" step="0.01" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                                <p v-if="paymentForm.errors.amount" class="mt-1 text-sm text-red-500">{{ paymentForm.errors.amount }}</p>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Payment Date</label>
                                                    <input v-model="paymentForm.payment_date" type="date" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                                    <p v-if="paymentForm.errors.payment_date" class="mt-1 text-sm text-red-500">{{ paymentForm.errors.payment_date }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Payment Method</label>
                                                    <select v-model="paymentForm.payment_method" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                                        <option value="cash">Cash</option>
                                                        <option value="card">Card</option>
                                                        <option value="bank_transfer">Bank Transfer</option>
                                                        <option value="check">Check</option>
                                                    </select>
                                                    <p v-if="paymentForm.errors.payment_method" class="mt-1 text-sm text-red-500">{{ paymentForm.errors.payment_method }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">Transaction ID (Optional)</label>
                                                <input v-model="paymentForm.transaction_id" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. TXN-12345">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">Notes (Optional)</label>
                                                <textarea v-model="paymentForm.notes" rows="2" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                                <button type="submit" :disabled="paymentForm.processing" class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition-colors disabled:opacity-50">
                                    {{ paymentForm.processing ? 'Processing...' : 'Confirm Payment' }}
                                </button>
                                <button type="button" @click="closePaymentModal" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
