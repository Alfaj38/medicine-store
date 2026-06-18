<template>
    <AdminLayout>
        <template #header>Branch Approvals</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Pending Branch Registrations</h3>
                <p class="text-sm text-gray-500 mt-1">Branches created by Free plan companies that require super-admin approval.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Company</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Branch Name</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Address</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Requested On</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="branch in branches.data" :key="branch.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ branch.company?.name }}</div>
                                <div class="text-xs text-gray-500 capitalize">{{ branch.company?.subscription_plan }} Plan</div>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-gray-900">{{ branch.name }}</span>
                            </td>
                            <td class="p-4 text-sm text-gray-600">{{ branch.address }}</td>
                            <td class="p-4 text-sm">{{ new Date(branch.created_at).toLocaleDateString() }}</td>
                            <td class="p-4 text-right space-x-2">
                                <Link :href="route('admin.branch-approvals.approve', branch.id)" method="post" as="button" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded shadow">
                                    Approve
                                </Link>
                                <Link :href="route('admin.branch-approvals.reject', branch.id)" method="post" as="button" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded shadow">
                                    Reject
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="branches.data.length === 0">
                            <td colspan="5" class="p-6 text-center text-gray-500">No pending branch approvals.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="branches.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ branches.from }} to {{ branches.to }} of {{ branches.total }} entries
                </div>
                <Pagination :links="branches.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    branches: Object,
});
</script>
