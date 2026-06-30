<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    accounts: Array,
});

const showModal = ref(false);
const editingAccount = ref(null);

const form = useForm({
    name: '',
    type: 'expense',
    status: 'active',
    description: '',
});

const openModal = (account = null) => {
    if (account) {
        editingAccount.value = account;
        form.name = account.name;
        form.type = account.type;
        form.status = account.status;
        form.description = account.description;
    } else {
        editingAccount.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingAccount.value) {
        form.put(route('accounting.accounts.update', editingAccount.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('accounting.accounts.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteAccount = (id) => {
    if (confirm('Are you sure you want to delete this account? Transactions must be empty.')) {
        router.delete(route('accounting.accounts.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Chart of Accounts - SaaSMedi" />

    <AppLayout>
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Chart of Accounts</h1>
                    <p class="text-sm text-slate-500">Manage income and expense categories.</p>
                </div>
                <button @click="openModal()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Account
                </button>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Account Name</th>
                                <th class="px-6 py-4 font-semibold">Type</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="!accounts.length">
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">No accounts found.</td>
                            </tr>
                            <tr v-for="acc in accounts" :key="acc.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ acc.name }}</div>
                                    <div class="text-xs text-slate-500">{{ acc.description || '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="{'bg-emerald-100 text-emerald-700': acc.type === 'income' || acc.type === 'asset', 'bg-rose-100 text-rose-700': acc.type === 'expense' || acc.type === 'liability'}" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        {{ acc.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="{'bg-emerald-100 text-emerald-700': acc.status === 'active', 'bg-slate-100 text-slate-700': acc.status === 'inactive'}" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        {{ acc.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openModal(acc)" class="text-blue-600 hover:text-blue-800 font-medium text-xs mr-3">Edit</button>
                                    <button @click="deleteAccount(acc.id)" class="text-rose-600 hover:text-rose-800 font-medium text-xs">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900">{{ editingAccount ? 'Edit Account' : 'Add Account' }}</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Account Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="e.g. Utility Bills" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Account Type</label>
                        <select v-model="form.type" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="2" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Account' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
