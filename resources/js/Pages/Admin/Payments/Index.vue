<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    payments: Object
});

const isRejectModalOpen = ref(false);
const selectedPayment = ref(null);

const rejectForm = useForm({
    reason: ''
});

const openRejectModal = (payment) => {
    selectedPayment.value = payment;
    rejectForm.reason = '';
    isRejectModalOpen.value = true;
};

const closeRejectModal = () => {
    isRejectModalOpen.value = false;
    selectedPayment.value = null;
    rejectForm.reset();
};

const approvePayment = (payment) => {
    if (confirm('Are you sure you want to approve this payment and activate the subscription?')) {
        const form = useForm({});
        form.patch(route('admin.payments.approve', payment.id), {
            preserveScroll: true
        });
    }
};

const submitRejection = () => {
    rejectForm.patch(route('admin.payments.reject', selectedPayment.value.id), {
        preserveScroll: true,
        onSuccess: () => closeRejectModal()
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
};
</script>

<template>
    <Head title="Subscription Payments" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Subscription Payments
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="text-lg leading-6 font-medium text-slate-900">Payment Verification</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Review and verify manual subscription payments.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Company</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Payment Details</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ payment.company?.name || 'Unknown Company' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 uppercase font-bold">{{ payment.gateway }}</div>
                                        <div class="text-xs text-slate-500">TrxID: {{ payment.transaction_id }}</div>
                                        <div class="text-xs text-slate-500">Sender: {{ payment.sender_account_no || 'N/A' }}</div>
                                        <div v-if="payment.payment_proof_path" class="mt-1">
                                            <a :href="'/storage/' + payment.payment_proof_path" target="_blank" class="text-xs text-blue-600 hover:underline">View Proof</a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ formatCurrency(payment.amount) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ new Date(payment.created_at).toLocaleDateString() }}</div>
                                        <div class="text-xs text-slate-500">{{ new Date(payment.created_at).toLocaleTimeString() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="payment.status === 'pending'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                        <span v-else-if="payment.status === 'success'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            Approved
                                        </span>
                                        <span v-else-if="payment.status === 'failed'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div v-if="payment.status === 'pending'" class="flex justify-end space-x-2">
                                            <button @click="approvePayment(payment)" class="text-emerald-600 hover:text-emerald-900 font-bold bg-emerald-50 px-3 py-1 rounded">Approve</button>
                                            <button @click="openRejectModal(payment)" class="text-red-600 hover:text-red-900 font-bold bg-red-50 px-3 py-1 rounded">Reject</button>
                                        </div>
                                        <div v-else class="text-slate-400 italic text-xs">
                                            Processed
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="payments.data.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No payments found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="isRejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="closeRejectModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitRejection">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">
                                        Reject Payment
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 mb-4">
                                            Please provide a reason for rejecting this payment (e.g. Invalid TrxID, Incorrect Amount).
                                        </p>
                                        <textarea v-model="rejectForm.reason" rows="3" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-slate-300 rounded-md" placeholder="Rejection reason..." required></textarea>
                                        <p class="text-xs text-red-500 mt-1" v-if="rejectForm.errors.reason">{{ rejectForm.errors.reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="rejectForm.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                Reject Payment
                            </button>
                            <button type="button" @click="closeRejectModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
