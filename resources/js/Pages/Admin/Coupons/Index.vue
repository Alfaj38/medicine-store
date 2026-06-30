<template>
    <AdminLayout>
        <template #header>Promotion & Discount Engine</template>

        <div class="w-full mx-auto space-y-6">
            
            <!-- KPI Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Coupons</p>
                    <h3 class="text-3xl font-black text-slate-800">{{ stats.total }}</h3>
                </div>
                <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-5">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Active Now</p>
                    <h3 class="text-3xl font-black text-emerald-700">{{ stats.active }}</h3>
                </div>
                <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-100 p-5">
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Scheduled</p>
                    <h3 class="text-3xl font-black text-amber-700">{{ stats.scheduled }}</h3>
                </div>
                <div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-200 p-5 opacity-75">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Expired</p>
                    <h3 class="text-3xl font-black text-slate-600">{{ stats.expired }}</h3>
                </div>
            </div>

            <!-- Dashboard Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Coupon List</h2>
                        <p class="text-xs text-slate-500 mt-1">Manage and track all promotional campaigns</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <input type="text" placeholder="Search code or campaign..." class="flex-1 sm:w-64 rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <Link :href="route('admin.coupons.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-colors whitespace-nowrap flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            Create Coupon
                        </Link>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4">Coupon Code & Name</th>
                                <th class="p-4">Discount</th>
                                <th class="p-4">Usage</th>
                                <th class="p-4">Context</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-mono font-black text-indigo-700 text-base">{{ coupon.code }}</div>
                                    <div class="text-xs font-bold text-slate-500 mt-0.5">{{ coupon.name }}</div>
                                    <div v-if="coupon.campaign" class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded mt-1 inline-block">{{ coupon.campaign.name }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-800 text-base">
                                        <template v-if="coupon.discount_type === 'percentage'">{{ coupon.discount_value }}% OFF</template>
                                        <template v-else-if="coupon.discount_type === 'fixed'">৳{{ coupon.discount_value }} OFF</template>
                                        <template v-else-if="coupon.discount_type === 'free_trial'">{{ coupon.discount_value }} Days Free</template>
                                        <template v-else><span class="capitalize">{{ coupon.discount_type.replace('_', ' ') }}</span></template>
                                    </div>
                                    <div v-if="coupon.max_discount_amount" class="text-[10px] text-slate-500 font-medium mt-0.5">Up to ৳{{ coupon.max_discount_amount }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500" :style="`width: ${coupon.max_uses_total ? (coupon.total_uses / coupon.max_uses_total) * 100 : 0}%`"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-600">
                                            {{ coupon.total_uses }} <span v-if="coupon.max_uses_total" class="text-slate-400">/ {{ coupon.max_uses_total }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[10px] font-bold uppercase">{{ coupon.subscription_type.replace('_', ' ') }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                        :class="{
                                            'bg-emerald-100 text-emerald-700': coupon.status === 'active',
                                            'bg-amber-100 text-amber-700': coupon.status === 'scheduled',
                                            'bg-slate-100 text-slate-500': coupon.status === 'expired',
                                            'bg-red-100 text-red-700': coupon.status === 'disabled'
                                        }">
                                        {{ coupon.status }}
                                    </span>
                                    <div v-if="coupon.expire_date" class="text-[10px] text-slate-400 font-medium mt-1">Exp: {{ new Date(coupon.expire_date).toLocaleDateString() }}</div>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="coupons.data.length === 0">
                                <td colspan="6" class="p-12 text-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700 mb-1">No coupons found</h4>
                                    <p class="text-xs text-slate-500 mb-4">Start by creating your first promotional campaign.</p>
                                    <Link :href="route('admin.coupons.create')" class="text-indigo-600 font-bold text-sm hover:underline">Create Coupon</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="coupons.links && coupons.links.length > 3" class="p-4 border-t border-slate-100 flex justify-center bg-white">
                    <div class="flex gap-1">
                        <Link v-for="(link, i) in coupons.links" :key="i" :href="link.url || '#'" 
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                            :class="link.active ? 'bg-indigo-600 text-white shadow-sm' : (link.url ? 'bg-slate-50 text-slate-600 hover:bg-slate-100' : 'bg-transparent text-slate-300 cursor-not-allowed')"
                            v-html="link.label">
                        </Link>
                    </div>
                </div>
            </div>
            
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

defineProps({
    coupons: Object,
    campaigns: Array,
    stats: Object,
});
</script>
