<template>
    <AdminLayout>
        <template #header>Security Center: Audit Logs</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800">System Audit Trail</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">User</th>
                            <th class="p-4">Action</th>
                            <th class="p-4">IP Address</th>
                            <th class="p-4">Entity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-slate-500 font-mono text-xs">{{ new Date(log.created_at).toLocaleString() }}</td>
                            <td class="p-4 font-bold text-slate-800">
                                {{ log.user?.name || 'System' }}
                                <span v-if="log.user" class="text-xs font-normal text-slate-500 block">{{ log.user.email }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-slate-100 text-slate-700 capitalize">
                                    {{ log.action }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs text-slate-500">{{ log.ip_address || 'N/A' }}</td>
                            <td class="p-4 text-slate-600">
                                <span v-if="log.entity_type">{{ log.entity_type }} #{{ log.entity_id }}</span>
                                <span v-else class="text-slate-400 italic">N/A</span>
                            </td>
                        </tr>
                        <tr v-if="logs.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No audit logs found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center" v-if="logs.data.length > 0">
                <div class="text-xs font-medium text-slate-500">
                    Showing {{ logs.from }} to {{ logs.to }} of {{ logs.total }} entries
                </div>
                <Pagination :links="logs.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    logs: Object,
});
</script>
