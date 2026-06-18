<template>
    <AdminLayout>
        <template #header>Companies</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">All Registered Companies</h3>
                
                <div class="flex space-x-2">
                    <select v-model="filters.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <select v-model="filters.plan" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                        <option value="">All Plans</option>
                        <option value="free">Free</option>
                        <option value="starter">Starter</option>
                        <option value="professional">Professional</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Company Name</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Plan</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Branches</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Users</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="company in companies.data" :key="company.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ company.name }}</div>
                                <div class="text-sm text-gray-500">{{ company.email }}</div>
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm font-medium">{{ company.subscription_plan }}</span>
                            </td>
                            <td class="p-4">{{ company.branches_count }}</td>
                            <td class="p-4">{{ company.users_count }}</td>
                            <td class="p-4">
                                <span v-if="company.registration_status === 'active'" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium uppercase tracking-wide">Active</span>
                                <span v-else-if="company.registration_status === 'pending'" class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium uppercase tracking-wide">Pending</span>
                                <span v-else-if="company.registration_status === 'suspended'" class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium uppercase tracking-wide">Suspended</span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <Link :href="route('admin.companies.show', company.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</Link>
                            </td>
                        </tr>
                        <tr v-if="companies.data.length === 0">
                            <td colspan="6" class="p-6 text-center text-gray-500">No companies found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="companies.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ companies.from }} to {{ companies.to }} of {{ companies.total }} entries
                </div>
                <Pagination :links="companies.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    companies: Object,
    filters: Object,
});

const filters = reactive({
    status: props.filters.status || '',
    plan: props.filters.plan || '',
});

const applyFilters = () => {
    router.get(route('admin.companies.index'), filters, { preserveState: true, replace: true });
};
</script>
