<template>
    <AdminLayout>
        <template #header>Feature Flags</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Platform Feature Flags</h3>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm font-medium hover:bg-indigo-700">Add Feature Flag</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Feature Key</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Company ID (if isolated)</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="flag in featureFlags.data" :key="flag.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900 font-mono text-sm">{{ flag.feature_key }}</div>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                {{ flag.company_id ? flag.company_id : 'Global' }}
                            </td>
                            <td class="p-4">
                                <span v-if="flag.is_enabled" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium uppercase tracking-wide">Enabled</span>
                                <span v-else class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium uppercase tracking-wide">Disabled</span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Toggle</button>
                                <button class="text-red-600 hover:text-red-900 text-sm font-medium ml-2">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="featureFlags.data.length === 0">
                            <td colspan="4" class="p-6 text-center text-gray-500">No feature flags found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="featureFlags.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ featureFlags.from }} to {{ featureFlags.to }} of {{ featureFlags.total }} entries
                </div>
                <Pagination :links="featureFlags.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    featureFlags: Object,
});
</script>
