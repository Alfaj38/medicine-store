<template>
    <AdminLayout>
        <template #header>Reseller Details</template>

        <div class="mb-6">
            <Link :href="route('admin.affiliate.resellers')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                &larr; Back to Resellers
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Profile</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div class="font-medium">{{ reseller.name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Business Name</div>
                        <div class="font-medium">{{ reseller.business_name || '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div class="font-medium">{{ reseller.email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Phone</div>
                        <div class="font-medium">{{ reseller.phone }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">NID / Trade License</div>
                        <div class="font-medium">{{ reseller.nid_or_business_id }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Commission Rate</div>
                        <div class="font-medium text-emerald-600">{{ Number(reseller.commission_rate) }}%</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Bank Information</h3>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-500">Method</div>
                        <div class="font-medium capitalize">{{ reseller.bank_info?.method }}</div>
                    </div>
                    <div v-if="reseller.bank_info?.method === 'bank'">
                        <div class="text-sm text-gray-500">Bank Name</div>
                        <div class="font-medium">{{ reseller.bank_info?.bank_name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Account Name</div>
                        <div class="font-medium">{{ reseller.bank_info?.account_name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Account Number</div>
                        <div class="font-medium font-mono">{{ reseller.bank_info?.account_number }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Codes Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Promo Codes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Code</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Label</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Companies</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Revenue Generated</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="code in reseller.promo_codes" :key="code.id" class="border-b">
                            <td class="p-4 font-mono text-sm">{{ code.code }}</td>
                            <td class="p-4 text-sm">{{ code.label }}</td>
                            <td class="p-4 text-sm">{{ code.total_companies }}</td>
                            <td class="p-4 text-sm">৳{{ Number(code.total_revenue).toLocaleString() }}</td>
                            <td class="p-4 text-sm capitalize">{{ code.status }}</td>
                        </tr>
                        <tr v-if="!reseller.promo_codes?.length">
                            <td colspan="5" class="p-4 text-center text-gray-500">No promo codes.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';

const props = defineProps({
    reseller: Object,
});
</script>
