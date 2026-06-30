<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    transactions: Array,
    accounts: Array,
    summary: Object,
    filters: Object,
});

const showModal = ref(false);
const editingTransaction = ref(null);

const form = useForm({
    account_id: '',
    type: 'expense',
    amount: '',
    date: new Date().toISOString().split('T')[0],
    reference: '',
    notes: '',
});

const openModal = (transaction = null) => {
    if (transaction) {
        editingTransaction.value = transaction;
        form.account_id = transaction.account_id;
        form.type = transaction.type;
        form.amount = transaction.amount;
        form.date = transaction.date;
        form.reference = transaction.reference;
        form.notes = transaction.notes;
    } else {
        editingTransaction.value = null;
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
    if (editingTransaction.value) {
        form.put(route('accounting.transactions.update', editingTransaction.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('accounting.transactions.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteTransaction = (id) => {
    if (confirm('Are you sure you want to delete this transaction?')) {
        router.delete(route('accounting.transactions.destroy', id), {
            preserveScroll: true,
        });
    }
};

const updateFilters = (key, value) => {
    let newFilters = { ...props.filters, [key]: value };
    router.get(route('accounting.transactions.index'), newFilters, { preserveScroll: true });
};

const changeMonth = (delta) => {
    let m = props.filters.month + delta;
    let y = props.filters.year;
    if (m > 12) { m = 1; y++; }
    if (m < 1) { m = 12; y--; }
    updateFilters('month', m);
    updateFilters('year', y);
};
</script>

<template>
    <Head title="Income & Expenses - SaaSMedi" />

    <AppLayout>
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Income & Expenses</h1>
                    <p class="text-sm text-slate-500">Track and manage your daily transactions.</p>
                </div>
                
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <!-- Month Navigation -->
                    <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                        <button @click="changeMonth(-1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span class="px-4 text-sm font-bold text-slate-700 min-w-[150px] text-center uppercase tracking-wider">
                            {{ new Date(filters.year, filters.month - 1).toLocaleString('default', { month: 'long', year: 'numeric' }) }}
                        </span>
                        <button @click="changeMonth(1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    
                    <button @click="openModal()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Transaction
                    </button>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-500">Total Income (Manual)</div>
                        <div class="text-2xl font-bold text-slate-900">${{ parseFloat(summary.total_income).toFixed(2) }}</div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-500">Total Expenses</div>
                        <div class="text-2xl font-bold text-slate-900">${{ parseFloat(summary.total_expense).toFixed(2) }}</div>
                    </div>
                </div>
                
                <div class="bg-indigo-600 rounded-xl shadow-sm p-6 flex items-center text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10">
                        <svg class="w-32 h-32 -mr-8 -mt-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
                    </div>
                    <div class="z-10 relative">
                        <div class="text-sm font-medium text-indigo-100 uppercase tracking-wider mb-1">Net Flow</div>
                        <div class="text-3xl font-bold">${{ parseFloat(summary.net_profit).toFixed(2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-4 flex items-center gap-2">
                <button @click="updateFilters('type', null)" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors" :class="!filters.type ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">All</button>
                <button @click="updateFilters('type', 'income')" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors" :class="filters.type === 'income' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">Income</button>
                <button @click="updateFilters('type', 'expense')" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors" :class="filters.type === 'expense' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">Expenses</button>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">Account / Category</th>
                                <th class="px-6 py-4 font-semibold">Reference & Notes</th>
                                <th class="px-6 py-4 font-semibold text-right">Amount</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="!transactions.length">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">No transactions found for this period.</td>
                            </tr>
                            <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                    {{ tx.date }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ tx.account.name }}</div>
                                    <span :class="tx.type === 'income' ? 'text-emerald-600' : 'text-rose-600'" class="text-[10px] font-bold uppercase tracking-wider">{{ tx.type }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="tx.reference" class="text-xs font-semibold text-slate-700 mb-0.5">Ref: {{ tx.reference }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-2 max-w-xs">{{ tx.notes || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold" :class="tx.type === 'income' ? 'text-emerald-600' : 'text-rose-600'">
                                    {{ tx.type === 'income' ? '+' : '-' }}${{ parseFloat(tx.amount).toFixed(2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openModal(tx)" class="text-blue-600 hover:text-blue-800 font-medium text-xs mr-3">Edit</button>
                                    <button @click="deleteTransaction(tx.id)" class="text-rose-600 hover:text-rose-800 font-medium text-xs">Delete</button>
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
                    <h3 class="text-lg font-bold text-slate-900">{{ editingTransaction ? 'Edit Transaction' : 'Add Transaction' }}</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-1 rounded-lg">
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.type" value="income" class="peer sr-only">
                            <div class="text-center py-2 text-sm font-semibold rounded-md transition-all peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm text-slate-500">Income</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.type" value="expense" class="peer sr-only">
                            <div class="text-center py-2 text-sm font-semibold rounded-md transition-all peer-checked:bg-white peer-checked:text-rose-700 peer-checked:shadow-sm text-slate-500">Expense</div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Account Category</label>
                        <select v-model="form.account_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="" disabled>Select Account</option>
                            <option v-for="acc in accounts.filter(a => form.type === 'expense' ? a.type==='expense' || a.type==='liability' : a.type==='income' || a.type==='asset')" :key="acc.id" :value="acc.id">
                                {{ acc.name }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium">$</span>
                                <input v-model="form.amount" type="number" step="0.01" min="0" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm pl-7" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Date</label>
                            <input v-model="form.date" type="date" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Reference No. (Optional)</label>
                        <input v-model="form.reference" type="text" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Voucher or Receipt No">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Notes</label>
                        <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Additional details..."></textarea>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Transaction' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
