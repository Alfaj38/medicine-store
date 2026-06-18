<template>
    <AdminLayout>
        <template #header>All Users</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Platform Users Across All Companies</h3>
                
                <div class="w-64">
                    <input type="text" v-model="search" @keyup.enter="applySearch" placeholder="Search by name or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">User</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Company & Branch</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Role Type</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Data Scope</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">
                                    {{ user.name }}
                                    <span v-if="user.is_company_owner" class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded ml-2">Owner</span>
                                </div>
                                <div class="text-sm text-gray-500">{{ user.email }}</div>
                            </td>
                            <td class="p-4">
                                <div v-if="user.company" class="font-medium text-sm text-gray-900">{{ user.company.name }}</div>
                                <div v-else class="text-sm text-gray-500 font-medium">Platform Admin</div>
                                <div v-if="user.branch" class="text-xs text-gray-500">{{ user.branch.name }}</div>
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm">{{ user.user_type }}</span>
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm">{{ user.data_scope }}</span>
                            </td>
                            <td class="p-4 text-sm">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="p-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="users.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ users.from }} to {{ users.to }} of {{ users.total }} entries
                </div>
                <Pagination :links="users.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const applySearch = () => {
    router.get(route('admin.users.index'), { search: search.value }, { preserveState: true, replace: true });
};
</script>
