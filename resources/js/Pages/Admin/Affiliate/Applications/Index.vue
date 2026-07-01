<template>
    <Head title="Affiliate Applications" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Pending Affiliate Applications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="text-lg leading-6 font-medium text-slate-900">Review Applications</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Approve or reject new reseller applications.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Applicant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Applied Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr v-for="application in applications.data" :key="application.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900">{{ application.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ application.email }}</div>
                                        <div class="text-xs text-slate-500">{{ application.phone || 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ new Date(application.created_at).toLocaleDateString() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 uppercase tracking-wider">
                                            {{ application.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button @click="approveApplication(application)" class="text-emerald-600 hover:text-emerald-900 font-bold bg-emerald-50 px-3 py-1 rounded">Approve</button>
                                            <button @click="rejectApplication(application)" class="text-red-600 hover:text-red-900 font-bold bg-red-50 px-3 py-1 rounded">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="applications.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No pending applications found.
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

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    applications: Object
});

const approveApplication = (application) => {
    Swal.fire({
        title: 'Approve Application?',
        text: `Are you sure you want to approve ${application.name} as a reseller?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, approve'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = useForm({});
            form.patch(route('admin.affiliates.approve', application.id), {
                preserveScroll: true
            });
        }
    });
};

const rejectApplication = (application) => {
    Swal.fire({
        title: 'Reject Application',
        input: 'textarea',
        inputLabel: 'Reason for rejection (sent to applicant)',
        inputPlaceholder: 'Enter your reason here...',
        inputAttributes: {
            'aria-label': 'Reason for rejection'
        },
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Reject',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write something!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = useForm({
                reason: result.value
            });
            form.patch(route('admin.affiliates.reject', application.id), {
                preserveScroll: true
            });
        }
    });
};
</script>
