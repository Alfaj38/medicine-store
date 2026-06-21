<template>
    <AdminLayout>
        <template #header>Affiliate Applications</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Pending Applications</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Applicant</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Business / ID</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Contact</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Bank Info</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Date Applied</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="app in applications.data" :key="app.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ app.name }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-sm">{{ app.business_name || '-' }}</div>
                                <div class="text-xs text-gray-500">ID: {{ app.nid_or_business_id }}</div>
                            </td>
                            <td class="p-4 text-sm">
                                <div>{{ app.email }}</div>
                                <div>{{ app.phone }}</div>
                            </td>
                            <td class="p-4 text-sm capitalize">
                                {{ app.bank_info?.method }}
                            </td>
                            <td class="p-4 text-sm text-gray-500">
                                {{ new Date(app.created_at).toLocaleDateString() }}
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <button @click="approve(app.id)" class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">Approve</button>
                                <button @click="reject(app.id)" class="text-red-600 hover:text-red-900 text-sm font-medium">Reject</button>
                            </td>
                        </tr>
                        <tr v-if="applications.data.length === 0">
                            <td colspan="6" class="p-6 text-center text-gray-500">No pending applications.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="applications.data.length > 0">
                <Pagination :links="applications.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    applications: Object,
});

const approve = (id) => {
    window.Swal.fire({
        title: 'Approve Application?',
        text: 'Are you sure you want to approve this affiliate application?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('admin.affiliate.approve', id), {}, { preserveScroll: true });
        }
    });
};

const reject = (id) => {
    window.Swal.fire({
        title: 'Reject Application',
        text: 'Enter reason for rejection:',
        input: 'textarea',
        inputPlaceholder: 'Reason for rejection...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, reject it!',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write a reason!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            router.patch(route('admin.affiliate.reject', id), { reason: result.value }, { preserveScroll: true });
        }
    });
};
</script>
