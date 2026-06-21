<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    withdrawals: Object,
    balance: [Number, String],
    bank_info: Object
});

const isModalOpen = ref(false);

const form = useForm({
    amount: '',
    payment_method: props.bank_info?.method || 'bank'
});

const submit = () => {
    form.post(route('reseller.withdrawals.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset('amount');
        }
    });
};
</script>

<template>
    <Head title="Withdrawals - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">Withdrawals</h2>
            <button @click="isModalOpen = true" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Request Payout
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold">Request ID</th>
                            <th class="p-4 font-bold">Method</th>
                            <th class="p-4 font-bold text-right">Amount</th>
                            <th class="p-4 font-bold text-center">Status</th>
                            <th class="p-4 font-bold">Admin Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="wd in withdrawals.data" :key="wd.id" class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm text-slate-600">
                                {{ new Date(wd.created_at).toLocaleString() }}
                            </td>
                            <td class="p-4 font-mono text-sm text-slate-500">
                                #{{ String(wd.id).padStart(5, '0') }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 capitalize">{{ wd.payment_method }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ wd.payment_details.account_number }}
                                </div>
                            </td>
                            <td class="p-4 text-right font-black text-slate-900">
                                ৳{{ Number(wd.amount).toLocaleString() }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                    :class="{
                                        'bg-amber-100 text-amber-800': wd.status === 'pending',
                                        'bg-blue-100 text-blue-800': wd.status === 'approved',
                                        'bg-emerald-100 text-emerald-800': wd.status === 'paid',
                                        'bg-red-100 text-red-800': wd.status === 'rejected'
                                    }">
                                    {{ wd.status }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-slate-500 truncate max-w-xs" :title="wd.admin_note">
                                {{ wd.admin_note || '-' }}
                            </td>
                        </tr>
                        <tr v-if="!withdrawals.data.length">
                            <td colspan="6" class="p-8 text-center text-slate-500 font-medium">No withdrawal requests found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50" v-if="withdrawals.links">
                <div class="flex items-center gap-1">
                    <Link v-for="(link, i) in withdrawals.links" :key="i" :href="link.url || '#'" 
                        class="px-3 py-1 rounded border text-sm font-medium transition-colors"
                        :class="[link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>

        <!-- Payout Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative">
                <button @click="isModalOpen = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200">✕</button>
                
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-xl font-black text-slate-900">Request Payout</h3>
                    <p class="text-sm text-slate-500 mt-1">Available balance: <strong class="text-emerald-600">৳{{ Number(balance).toLocaleString() }}</strong></p>
                </div>
                
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Amount (৳)</label>
                            <input v-model="form.amount" type="number" min="500" :max="balance" step="1" required class="w-full rounded-xl border border-slate-300 px-4 py-3 font-mono font-bold text-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <p class="text-xs text-slate-500 mt-1">Minimum withdrawal: ৳500</p>
                            <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1 font-semibold">{{ form.errors.amount }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Payout Method</label>
                            <select v-model="form.payment_method" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 bg-white">
                                <option value="bank">Bank Transfer</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                            </select>
                            <p v-if="form.errors.payment_method" class="text-red-500 text-xs mt-1">{{ form.errors.payment_method }}</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl">
                            <p class="text-xs text-blue-800 leading-relaxed font-medium">Funds will be sent to the account details provided during your partner registration. If you need to update them, please contact support.</p>
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isModalOpen = false" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
                            <button type="submit" :disabled="form.processing || Number(form.amount) < 500 || Number(form.amount) > Number(balance)" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all disabled:opacity-50 text-sm">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
