<template>
    <AdminLayout>
        <template #header>Platform Settings</template>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Global Settings</h3>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Platform Name</label>
                    <input type="text" value="MediSaaS" class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email (for Support Tickets)</label>
                    <input type="email" value="support@medisaas.com" class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Allow new company registrations</span>
                    </label>
                </div>

                <div class="pt-4 border-t">
                    <button type="button" class="px-4 py-2 bg-indigo-600 text-white rounded shadow text-sm font-medium hover:bg-indigo-700">Save Settings</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';

defineProps({
    settings: Object,
});
</script>
