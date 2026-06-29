<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    reseller: Object
});

const form = useForm({
    name: props.reseller.name || '',
    phone: props.reseller.phone || '',
    bank_info: props.reseller.bank_info || {
        method: 'bank',
        account_name: '',
        account_number: '',
        bank_name: '',
        branch_name: ''
    }
});

const submit = () => {
    form.post(route('reseller.settings.update'), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Account Settings - Partner Portal" />
    <ResellerLayout>
        
        <div class="flex flex-col mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Account Settings</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Manage your personal profile and payout methods.</p>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column (Profile & Contact) -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-[15px] text-slate-800">Profile Information</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Full Name / Agency Name</label>
                            <input v-model="form.name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" required>
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Phone Number</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" required>
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Email Address</label>
                            <input :value="reseller.email" type="email" disabled class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-500 bg-slate-100 cursor-not-allowed">
                            <p class="text-[11px] text-slate-400 mt-1 font-medium">Email address cannot be changed.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (Payout Details) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-[15px] text-slate-800">Payout Details</h3>
                        <span class="px-2.5 py-1 bg-[#00b67a]/10 text-[#00b67a] rounded-lg text-[10px] font-bold uppercase tracking-wider">Secure</span>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-3">Preferred Payout Method</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <label v-for="method in ['bank', 'bkash', 'nagad', 'rocket']" :key="method" 
                                    class="relative border rounded-xl p-3 flex flex-col items-center cursor-pointer transition-all"
                                    :class="form.bank_info.method === method ? 'border-[#00b67a] bg-emerald-50/50 text-[#00b67a]' : 'border-slate-200 hover:border-slate-300 bg-white text-slate-500'">
                                    <input type="radio" :value="method" v-model="form.bank_info.method" class="sr-only">
                                    <span class="font-bold text-xs capitalize mt-1">{{ method === 'bank' ? 'Bank Transfer' : method }}</span>
                                    <div v-if="form.bank_info.method === method" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-[#00b67a] rounded-full text-white flex items-center justify-center border-2 border-white">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Mobile Financial Services (bKash/Nagad/Rocket) Fields -->
                        <div v-if="form.bank_info.method !== 'bank'" class="grid grid-cols-1 sm:grid-cols-2 gap-5 animate-in fade-in zoom-in-95 duration-200">
                            <div class="sm:col-span-2">
                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5 capitalize">{{ form.bank_info.method }} Account Number</label>
                                <input v-model="form.bank_info.account_number" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" :placeholder="'e.g. 01700000000'">
                            </div>
                        </div>

                        <!-- Bank Transfer Fields -->
                        <div v-if="form.bank_info.method === 'bank'" class="grid grid-cols-1 sm:grid-cols-2 gap-5 animate-in fade-in zoom-in-95 duration-200">
                            <div>
                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Bank Name</label>
                                <input v-model="form.bank_info.bank_name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" placeholder="e.g. Dutch Bangla Bank">
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Branch Name</label>
                                <input v-model="form.bank_info.branch_name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" placeholder="e.g. Banani Branch">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Account Name</label>
                                <input v-model="form.bank_info.account_name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white" placeholder="Name on the bank account">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Account Number</label>
                                <input v-model="form.bank_info.account_number" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:border-[#00b67a] focus:ring-1 focus:ring-[#00b67a] transition-all bg-slate-50 focus:bg-white">
                            </div>
                        </div>

                    </div>
                    <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <p class="text-[11px] font-medium text-slate-500 max-w-sm">Please ensure these details are accurate to avoid delays in receiving your commissions.</p>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-[#00b67a] hover:bg-[#00a06b] text-white rounded-xl font-bold text-sm shadow-sm shadow-[#00b67a]/20 transition-all disabled:opacity-50">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </ResellerLayout>
</template>
