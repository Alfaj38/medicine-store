<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    requisition: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'Draft': 'bg-gray-100 text-gray-800',
        'Submitted': 'bg-blue-100 text-blue-800',
        'Store Approved': 'bg-purple-100 text-purple-800',
        'Accounts Approved': 'bg-indigo-100 text-indigo-800',
        'Owner Approved': 'bg-emerald-100 text-emerald-800',
        'PO Generated': 'bg-green-100 text-green-800',
        'Cancelled': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const updateStatus = (newStatus) => {
    Swal.fire({
        title: `Change status to ${newStatus}?`,
        text: "This will update the requisition state in the workflow.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('requisitions.status', props.requisition.id), { status: newStatus }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Requisition status has been updated.',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="`Requisition ${requisition.requisition_no}`" />

        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('requisitions.index')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-3">
                        Requisition Details 
                        <span class="text-sm font-normal text-slate-500">#{{ requisition.requisition_no }}</span>
                    </h2>
                </div>
                <div>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full" :class="getStatusColor(requisition.status)">
                        {{ requisition.status }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6 md:col-span-2">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">General Information</h3>
                        <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                            <div>
                                <div class="text-xs font-semibold text-slate-500 uppercase">Created By</div>
                                <div class="mt-1 text-sm text-slate-900 font-medium">{{ requisition.creator?.name }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-500 uppercase">Date Created</div>
                                <div class="mt-1 text-sm text-slate-900 font-medium">{{ new Date(requisition.created_at).toLocaleString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-500 uppercase">Requisition Type</div>
                                <div class="mt-1 text-sm text-slate-900 font-medium">{{ requisition.type }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-500 uppercase">Priority</div>
                                <div class="mt-1 text-sm font-medium" :class="requisition.priority === 'Urgent' ? 'text-rose-600' : 'text-slate-900'">{{ requisition.priority }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-500 uppercase">Required Date</div>
                                <div class="mt-1 text-sm text-slate-900 font-medium">{{ requisition.required_date ? new Date(requisition.required_date).toLocaleDateString() : 'N/A' }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-xs font-semibold text-slate-500 uppercase">Internal Notes</div>
                                <div class="mt-1 text-sm text-slate-700 bg-slate-50 p-3 rounded-lg">{{ requisition.notes || 'No notes provided.' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">Workflow Actions</h3>
                        
                        <div class="space-y-3">
                            <button v-if="requisition.status === 'Draft'" @click="updateStatus('Submitted')" class="w-full text-left px-4 py-3 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold transition-colors flex items-center justify-between">
                                Submit for Approval
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>

                            <button v-if="requisition.status === 'Submitted'" @click="updateStatus('Store Approved')" class="w-full text-left px-4 py-3 rounded-xl border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold transition-colors flex items-center justify-between">
                                Store Approval
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            
                            <button v-if="requisition.status === 'Store Approved'" @click="updateStatus('Accounts Approved')" class="w-full text-left px-4 py-3 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors flex items-center justify-between">
                                Accounts Approval (Budget)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>

                            <button v-if="requisition.status === 'Accounts Approved'" @click="updateStatus('Owner Approved')" class="w-full text-left px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition-colors flex items-center justify-between">
                                Owner Final Approval
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            
                            <button v-if="requisition.status === 'Owner Approved'" @click="updateStatus('PO Generated')" class="w-full text-left px-4 py-3 rounded-xl border border-green-200 bg-green-50 hover:bg-green-100 text-green-700 font-semibold transition-colors flex items-center justify-between">
                                Generate Purchase Order
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>

                            <button v-if="!['PO Generated', 'Cancelled'].includes(requisition.status)" @click="updateStatus('Cancelled')" class="w-full text-left px-4 py-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition-colors flex items-center justify-between mt-6">
                                Cancel Requisition
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            
                            <div v-if="['PO Generated', 'Cancelled'].includes(requisition.status)" class="text-center p-4 bg-slate-50 rounded-xl text-slate-500 text-sm font-medium">
                                Workflow Completed / Closed.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                    <div class="p-6 bg-white border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Requested Items</h3>
                        <span class="text-sm font-medium text-slate-500">{{ requisition.items.length }} Items Total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Item Name</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Current Stock</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Requested Qty</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Approved Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-for="item in requisition.items" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900">{{ item.item?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ item.current_stock }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">{{ item.requested_qty }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ item.approved_qty }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
