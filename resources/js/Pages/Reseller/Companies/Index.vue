<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';
import Pagination from '@/Components/Pagination.vue'; // Assuming standard Jetstream/Breeze pagination or simple custom one
// We'll create a simple Pagination block locally if needed.

const props = defineProps({
    companies: Object
});
</script>

<template>
    <Head title="Referred Companies - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">Referred Companies</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold">Company Name</th>
                            <th class="p-4 font-bold">Registration Date</th>
                            <th class="p-4 font-bold">Used Code</th>
                            <th class="p-4 font-bold">Current Plan</th>
                            <th class="p-4 font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="ref in companies.data" :key="ref.id" class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ ref.company.name }}</div>
                                <div class="text-xs text-slate-500">{{ ref.company.phone }}</div>
                            </td>
                            <td class="p-4 text-sm text-slate-600">
                                {{ new Date(ref.registered_at).toLocaleDateString() }}
                            </td>
                            <td class="p-4">
                                <div class="font-mono text-xs font-bold bg-slate-100 px-2 py-1 rounded inline-block text-slate-700">
                                    {{ ref.referral_code?.code || 'Link' }}
                                </div>
                            </td>
                            <td class="p-4 text-sm font-medium text-slate-700">
                                {{ ref.company.subscription?.plan?.name || 'Unknown' }}
                                <span class="text-xs text-slate-500 block">{{ ref.company.subscription?.billing_cycle }}</span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                    :class="ref.company.subscription?.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'">
                                    {{ ref.company.subscription?.status || 'inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!companies.data.length">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No referred companies yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50" v-if="companies.links">
                <div class="flex items-center gap-1">
                    <Link v-for="(link, i) in companies.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1 rounded border text-sm font-medium transition-colors"
                        :class="[link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
