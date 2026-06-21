<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    codes: Array
});

const form = useForm({
    label: ''
});

const generate = () => {
    form.post(route('reseller.codes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const toggle = (id) => {
    useForm({}).patch(route('reseller.codes.toggle', id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Referral Codes - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">Referral Codes</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 class="font-bold text-lg text-slate-900">Generate New Code</h3>
                    <p class="text-sm text-slate-500">Create codes for different marketing campaigns.</p>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="generate" class="flex gap-4">
                    <input v-model="form.label" type="text" placeholder="Campaign Label (e.g. Facebook Ads Q3)" class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition-all disabled:opacity-70 whitespace-nowrap">
                        Generate Code
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold">Code</th>
                        <th class="p-4 font-bold">Label</th>
                        <th class="p-4 font-bold text-right">Referrals</th>
                        <th class="p-4 font-bold text-right">Commission Earned</th>
                        <th class="p-4 font-bold text-center">Status</th>
                        <th class="p-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="code in codes" :key="code.id" class="hover:bg-slate-50 transition-colors">
                        <td class="p-4">
                            <div class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-1 rounded inline-block text-sm">{{ code.code }}</div>
                        </td>
                        <td class="p-4 text-sm font-medium text-slate-700">{{ code.label }}</td>
                        <td class="p-4 text-right font-bold text-slate-900">{{ code.total_companies }}</td>
                        <td class="p-4 text-right font-bold text-emerald-600">৳{{ Number(code.total_commission).toLocaleString() }}</td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                :class="code.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'">
                                {{ code.status }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button @click="toggle(code.id)" class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors"
                                :class="code.status === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'">
                                {{ code.status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!codes.length">
                        <td colspan="6" class="p-8 text-center text-slate-500 font-medium">No referral codes found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </ResellerLayout>
</template>
