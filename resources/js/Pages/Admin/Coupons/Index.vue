<template>
    <AdminLayout>
        <template #header>Coupons</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800">Discount Coupons</h2>
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
                    + Create Coupon
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="p-4">Code</th>
                            <th class="p-4">Value</th>
                            <th class="p-4">Uses</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Expires</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-mono font-bold text-indigo-600 tracking-wider">{{ coupon.code }}</td>
                            <td class="p-4 font-bold text-slate-800">
                                {{ coupon.type === 'percentage' ? coupon.value + '%' : '৳' + coupon.value }}
                            </td>
                            <td class="p-4 text-slate-600">{{ coupon.uses }} / {{ coupon.max_uses || '∞' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold"
                                    :class="coupon.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">
                                    {{ coupon.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-500">{{ coupon.expires_at ? new Date(coupon.expires_at).toLocaleDateString() : 'Never' }}</td>
                        </tr>
                        <tr v-if="coupons.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No coupons have been created yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';

defineProps({
    coupons: Object,
});
</script>
