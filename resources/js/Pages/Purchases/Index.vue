<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    purchases: Object,
});

const isPaymentModalOpen = ref(false);
const selectedPurchase = ref(null);
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
    if (!selectedPurchase.value) return;
    const due = selectedPurchase.value.total_amount - selectedPurchase.value.paid_amount;
    let cashDue = due - totalSelectedCredit.value;
    if (cashDue < 0) cashDue = 0;
    paymentForm.amount = cashDue.toFixed(2);
}, { deep: true });

const openPaymentModal = async (purchase) => {
    selectedPurchase.value = purchase;
    const due = purchase.total_amount - purchase.paid_amount;
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
        const response = await fetch(route('purchases.pending-credit-notes', purchase.supplier_id));
        pendingCreditNotes.value = await response.json();
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingCreditNotes.value = false;
    }
};

const closePaymentModal = () => {
    isPaymentModalOpen.value = false;
    selectedPurchase.value = null;
    paymentForm.reset();
    paymentForm.clearErrors();
};

const submitPayment = () => {
    if (!selectedPurchase.value) return;
    
    paymentForm.post(route('purchases.pay', selectedPurchase.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closePaymentModal();
        },
    });
};
</script>

<template>
    <Head title="Purchases - MediSaaS" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-500 shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <span class="font-bold text-xl tracking-tight text-slate-800">MediSaaS</span>
                        </div>
                        <div class="hidden sm:flex space-x-8 h-full">
                            <Link :href="route('dashboard')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url === '/dashboard' ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Dashboard
                            </Link>
                            <Link :href="route('medicines.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/medicines') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Master Data
                            </Link>
                            <Link :href="route('suppliers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/suppliers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Suppliers
                            </Link>
                            <Link :href="route('purchases.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/purchases') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Purchases
                            </Link>
                            <Link :href="route('sales.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/sales') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Sales
                            </Link>
                            <Link :href="route('customers.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/customers') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Customers
                            </Link>
                            <Link :href="route('reports.expiry')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Reports
                            </Link>
                            <Link v-if="$page.props.auth.user.is_company_owner" :href="route('company.settings.index')" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors" :class="$page.url.startsWith('/company') ? 'border-emerald-500 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                                Settings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Purchases (IN)</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage medicine acquisitions and supplier invoices.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('purchase-returns.index')" class="inline-flex items-center justify-center rounded-xl bg-white border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                        </svg>
                        View Returns
                    </Link>
                    <Link :href="route('purchases.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Purchase
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Ref & Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Supplier</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Payment Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="purchase in purchases.data" :key="purchase.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="font-semibold text-slate-900">{{ purchase.reference_no }}</div>
                                    <div class="text-sm text-slate-500">{{ purchase.purchase_date }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-sm text-slate-900 font-medium">{{ purchase.supplier?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ purchase.supplier?.company_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right">
                                    <div class="text-sm font-bold text-slate-900">${{ parseFloat(purchase.total_amount).toFixed(2) }}</div>
                                    <div class="text-xs text-slate-500 mt-1 flex justify-end gap-2">
                                        <span>Paid: <span class="font-medium text-emerald-600">${{ parseFloat(purchase.paid_amount).toFixed(2) }}</span></span>
                                        <span v-if="(purchase.total_amount - purchase.paid_amount) > 0" class="text-slate-300">|</span>
                                        <span v-if="(purchase.total_amount - purchase.paid_amount) > 0">Due: <span class="font-bold text-rose-600">${{ (purchase.total_amount - purchase.paid_amount).toFixed(2) }}</span></span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span v-if="purchase.payment_status === 'paid'" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Paid</span>
                                        <span v-else-if="purchase.payment_status === 'partial'" class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Partial</span>
                                        <span v-else class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">Unpaid</span>
                                        <span v-if="purchase.returns_count > 0" class="inline-flex items-center gap-1 rounded-md bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-600/20">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/></svg>
                                            {{ purchase.returns_count }} Return{{ purchase.returns_count > 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 flex justify-end gap-3 items-center">
                                    <button 
                                        v-if="purchase.payment_status !== 'paid'"
                                        @click="openPaymentModal(purchase)" 
                                        class="text-amber-600 hover:text-amber-900 transition-colors font-semibold"
                                    >
                                        Pay
                                    </button>
                                    <Link :href="route('purchases.show', purchase.id)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                        View<span class="sr-only">, {{ purchase.reference_no }}</span>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="purchases.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <p class="text-lg font-medium text-slate-900">No purchases found</p>
                                    <p class="mt-1">Create a purchase order to stock up your inventory.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                            <p>Paying supplier <span class="font-bold text-slate-700">{{ selectedPurchase?.supplier?.name }}</span> for PO <span class="font-bold text-slate-700">{{ selectedPurchase?.reference_no }}</span>.</p>
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
