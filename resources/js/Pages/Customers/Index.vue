<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('customers.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const showPaymentModal = ref(false);
const selectedCustomer = ref(null);

const paymentForm = useForm({
    amount: '',
    payment_date: new Date().toISOString().slice(0, 10),
    payment_method: 'cash',
    notes: '',
});

const openPaymentModal = (customer) => {
    selectedCustomer.value = customer;
    paymentForm.amount = customer.current_balance > 0 ? customer.current_balance : '';
    showPaymentModal.value = true;
};

const processPayment = () => {
    paymentForm.post(route('customers.receivePayment', selectedCustomer.value.id), {
        onSuccess: () => {
            showPaymentModal.value = false;
            paymentForm.reset();
        }
    });
};
</script>

<template>
    <Head title="Customers - SaaSMedi" />

    <AppLayout>

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Customers Directory</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage regular customers, their details, and collect outstanding dues.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-4">
                    <Link :href="route('customers.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Customer
                    </Link>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow max-w-md group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Search by customer name or phone..." 
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                    >
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sm:pl-6">Customer Details</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Balance</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-inner border border-emerald-100">
                                            {{ customer.name.charAt(0) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-semibold text-slate-900">{{ customer.name }}</div>
                                            <div class="text-xs text-slate-500">Joined: {{ new Date(customer.created_at).toLocaleDateString() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-sm text-slate-900">{{ customer.phone || 'N/A' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right">
                                    <div class="text-sm font-semibold" :class="parseFloat(customer.current_balance) > 0 ? 'text-rose-600' : 'text-slate-900'">
                                        ${{ parseFloat(customer.current_balance).toFixed(2) }}
                                    </div>
                                    <div v-if="parseFloat(customer.current_balance) > 0">
                                        <button @click="openPaymentModal(customer)" class="text-xs font-medium text-emerald-600 hover:text-emerald-800 underline mt-1">Collect Payment</button>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-slate-500">
                                    <span v-if="customer.is_active" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">Inactive</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <Link :href="route('customers.edit', customer.id)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="customers.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-500">
                                    <p class="text-lg font-medium text-slate-900">No customers found</p>
                                    <p class="mt-1">Add regular customers to track their purchases and dues.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Payment Collection Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showPaymentModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Receive Payment from {{ selectedCustomer?.name }}</h3>
                
                <div class="mb-4 p-3 bg-rose-50 border border-rose-100 rounded-xl flex justify-between items-center">
                    <span class="text-sm font-medium text-rose-800">Current Due:</span>
                    <span class="text-lg font-bold text-rose-600">${{ parseFloat(selectedCustomer?.current_balance).toFixed(2) }}</span>
                </div>

                <form @submit.prevent="processPayment" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Amount Received</label>
                        <input v-model="paymentForm.amount" type="number" step="0.01" required class="mt-1 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Date</label>
                        <input v-model="paymentForm.payment_date" type="date" required class="mt-1 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Payment Method</label>
                        <select v-model="paymentForm.payment_method" class="mt-1 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Banking</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Notes (Optional)</label>
                        <textarea v-model="paymentForm.notes" rows="2" class="mt-1 block w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showPaymentModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" :disabled="paymentForm.processing" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
