<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    payments: Object
});

const rejectPayment = (payment) => {
    Swal.fire({
        title: 'Reject Payment',
        input: 'textarea',
        inputLabel: 'Reason for rejection (sent to client)',
        inputPlaceholder: 'Enter your reason here... (e.g. Invalid TrxID)',
        inputAttributes: {
            'aria-label': 'Reason for rejection'
        },
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Reject Payment',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write a reason!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = useForm({
                reason: result.value
            });
            form.patch(route('admin.payments.reject', payment.id), {
                preserveScroll: true
            });
        }
    });
};

const approvePayment = (payment) => {
    Swal.fire({
        title: 'Approve Payment?',
        text: 'Are you sure you want to approve this payment and activate the subscription?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = useForm({});
            form.patch(route('admin.payments.approve', payment.id), {
                preserveScroll: true
            });
        }
    });
};


const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' }).format(amount);
};
</script>

<template>
    <Head title="Subscription Payments" />

    <AdminLayout>
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
                                        <div v-if="payment.package" class="text-xs font-semibold text-indigo-600 mt-1">
                                            📦 {{ payment.package.name }} ({{ payment.billing_cycle || 'N/A' }})
                                            <span v-if="payment.billing_cycles > 1"> × {{ payment.billing_cycles }}</span>
                                        </div>
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
                                            <button @click="rejectPayment(payment)" class="text-red-600 hover:text-red-900 font-bold bg-red-50 px-3 py-1 rounded">Reject</button>
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



    </AdminLayout>
</template>
