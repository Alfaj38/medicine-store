<template>
    <AdminLayout>
        <template #header>Company Details: {{ company.name }}</template>

        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
                <div class="flex items-center space-x-4">
                    <span v-if="company.registration_status === 'active'" class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm font-medium">Active</span>
                    <span v-else-if="company.registration_status === 'pending'" class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-medium">Pending Approval</span>
                    <span v-else-if="company.registration_status === 'suspended'" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Suspended</span>
                    
                    <span class="text-sm text-gray-500">Plan: <strong class="capitalize">{{ company.subscription_plan }}</strong></span>
                </div>
                
                <div class="flex space-x-3">
                    <Link v-if="company.registration_status === 'pending'" :href="route('admin.companies.approve', company.id)" method="post" as="button" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded shadow">
                        Approve Company
                    </Link>
                    <Link v-if="company.registration_status === 'active'" :href="route('admin.companies.suspend', company.id)" method="post" as="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded shadow">
                        Suspend Company
                    </Link>
                    <Link v-if="company.registration_status === 'suspended'" :href="route('admin.companies.approve', company.id)" method="post" as="button" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded shadow">
                        Unsuspend (Reactivate)
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Company Profile</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Name</span> <span class="font-medium">{{ company.name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Email</span> <span class="font-medium">{{ company.email }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Phone</span> <span class="font-medium">{{ company.phone }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Joined Date</span> <span class="font-medium">{{ new Date(company.created_at).toLocaleDateString() }}</span></div>
                    </div>
                </div>
                
                <!-- Subscription Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Subscription Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Current Plan</span> <span class="font-medium capitalize">{{ company.subscription_plan }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Expires At</span> <span class="font-medium">{{ company.subscription_expires_at ? new Date(company.subscription_expires_at).toLocaleDateString() : 'N/A' }}</span></div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Usage Stats</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Branches</span> <span class="font-medium">{{ company.branches.length }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Total Users</span> <span class="font-medium">{{ company.users.length }}</span></div>
                    </div>
                </div>
            </div>
            
            <!-- Branches Tab -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Branches</h3>
                </div>
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50 border-b text-gray-600">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Address</th>
                            <th class="p-4">Phone</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="branch in company.branches" :key="branch.id" class="border-b">
                            <td class="p-4 font-medium text-gray-900">
                                {{ branch.name }} <span v-if="branch.is_default" class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded ml-2">HQ</span>
                            </td>
                            <td class="p-4">{{ branch.address }}</td>
                            <td class="p-4">{{ branch.phone }}</td>
                            <td class="p-4">
                                <span v-if="branch.approval_status === 'approved'" class="text-emerald-600 font-medium">Approved</span>
                                <span v-else-if="branch.approval_status === 'pending'" class="text-amber-600 font-medium">Pending</span>
                                <span v-else class="text-red-600 font-medium">Rejected</span>
                            </td>
                        </tr>
                        <tr v-if="company.branches.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500">No branches found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Users Tab -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Users</h3>
                </div>
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50 border-b text-gray-600">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Role Type</th>
                            <th class="p-4">Data Scope</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in company.users" :key="user.id" class="border-b">
                            <td class="p-4 font-medium text-gray-900">
                                {{ user.name }} <span v-if="user.is_company_owner" class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded ml-2">Owner</span>
                            </td>
                            <td class="p-4">{{ user.email }}</td>
                            <td class="p-4 capitalize">{{ user.user_type }}</td>
                            <td class="p-4 capitalize">{{ user.data_scope }}</td>
                        </tr>
                        <tr v-if="company.users.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

defineProps({
    company: Object,
});
</script>
