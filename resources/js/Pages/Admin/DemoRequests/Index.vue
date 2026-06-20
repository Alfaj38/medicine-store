<template>
    <AdminLayout>
        <template #header>Demo Requests</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div class="flex gap-3">
                    <input v-model="search" type="text" placeholder="Search leads..." class="w-64 rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <select v-model="status" class="rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="no_show">No-show</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="p-4">Contact</th>
                            <th class="p-4">Company</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date Requested</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="req in demoRequests.data" :key="req.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <p class="font-bold text-slate-800">{{ req.name }}</p>
                                <a :href="`mailto:${req.email}`" class="text-xs text-emerald-600 hover:underline">{{ req.email }}</a>
                            </td>
                            <td class="p-4 text-slate-600 font-medium">{{ req.company_name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold capitalize border"
                                    :class="{
                                        'bg-blue-50 text-blue-700 border-blue-200': req.status === 'pending',
                                        'bg-purple-50 text-purple-700 border-purple-200': req.status === 'confirmed',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200': req.status === 'completed',
                                        'bg-slate-50 text-slate-600 border-slate-200': req.status === 'no_show',
                                    }">
                                    {{ req.status.replace('_', '-') }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-500 text-xs">{{ new Date(req.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}</td>
                            <td class="p-4 text-right">
                                <Link :href="route('admin.demo-requests.show', req.id)" class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                                    View Details
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="demoRequests.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No demo requests found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end" v-if="demoRequests.links && demoRequests.data.length > 0">
                <Pagination :links="demoRequests.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    demoRequests: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

watch([search, status], debounce(() => {
    router.get(route('admin.demo-requests.index'), { search: search.value, status: status.value }, { preserveState: true, replace: true });
}, 300));
</script>
