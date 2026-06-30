<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    employees: Array,
    payrolls: Array,
    currentMonth: Number,
    currentYear: Number,
});

const showModal = ref(false);

const form = useForm({
    employee_id: '',
    month: props.currentMonth,
    year: props.currentYear,
    basic_salary: 0,
    bonus: 0,
    deduction: 0,
    payment_method: 'bank_transfer',
    paid_at: new Date().toISOString().split('T')[0],
    notes: '',
});

const openModal = (emp = null, existingPayroll = null) => {
    form.reset();
    form.month = props.currentMonth;
    form.year = props.currentYear;
    
    if (existingPayroll) {
        form.employee_id = existingPayroll.employee_id;
        form.basic_salary = existingPayroll.basic_salary;
        form.bonus = existingPayroll.bonus;
        form.deduction = existingPayroll.deduction;
        form.payment_method = existingPayroll.payment_method;
        form.paid_at = existingPayroll.paid_at;
        form.notes = existingPayroll.notes;
    } else if (emp) {
        form.employee_id = emp.id;
        form.basic_salary = emp.salary;
    }
    
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post(route('hr.payroll.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const changeMonth = (delta) => {
    let m = props.currentMonth + delta;
    let y = props.currentYear;
    if (m > 12) { m = 1; y++; }
    if (m < 1) { m = 12; y--; }
    router.get(route('hr.payroll.index'), { month: m, year: y }, { preserveScroll: true });
};

const getPayroll = (empId) => {
    return props.payrolls.find(p => p.employee_id === empId);
};
</script>

<template>
    <Head title="Payroll Processing - SaaSMedi" />

    <AppLayout>
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Payroll Processing</h1>
                    <p class="text-sm text-slate-500">Calculate and record monthly salaries.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                        <button @click="changeMonth(-1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span class="px-4 text-sm font-bold text-slate-700 min-w-[150px] text-center uppercase tracking-wider">
                            {{ new Date(currentYear, currentMonth - 1).toLocaleString('default', { month: 'long', year: 'numeric' }) }}
                        </span>
                        <button @click="changeMonth(1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Employee</th>
                                <th class="px-6 py-4 font-semibold text-right">Base Salary</th>
                                <th class="px-6 py-4 font-semibold text-right">Bonus</th>
                                <th class="px-6 py-4 font-semibold text-right">Deduction</th>
                                <th class="px-6 py-4 font-semibold text-right">Net Salary</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="!employees.length">
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500">No employees found.</td>
                            </tr>
                            <tr v-for="emp in employees" :key="emp.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ emp.name }}</div>
                                    <div class="text-xs text-slate-500">{{ emp.designation }}</div>
                                </td>
                                
                                <template v-if="getPayroll(emp.id)">
                                    <td class="px-6 py-4 text-right font-medium">${{ parseFloat(getPayroll(emp.id).basic_salary).toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-emerald-600">+${{ parseFloat(getPayroll(emp.id).bonus).toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-rose-600">-${{ parseFloat(getPayroll(emp.id).deduction).toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-900">${{ parseFloat(getPayroll(emp.id).net_salary).toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Paid</span>
                                        <div class="text-[10px] text-slate-500 mt-1">{{ getPayroll(emp.id).paid_at }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="openModal(emp, getPayroll(emp.id))" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</button>
                                    </td>
                                </template>
                                <template v-else>
                                    <td class="px-6 py-4 text-right font-medium">${{ parseFloat(emp.salary).toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-right text-slate-400">-</td>
                                    <td class="px-6 py-4 text-right text-slate-400">-</td>
                                    <td class="px-6 py-4 text-right text-slate-400">-</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Unpaid</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="openModal(emp)" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded text-xs font-semibold transition-colors">Pay Now</button>
                                    </td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payroll Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900">Process Salary</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div class="bg-slate-50 p-4 rounded-lg flex justify-between items-center mb-2 border border-slate-100">
                        <div class="text-sm font-medium text-slate-500">Net Salary</div>
                        <div class="text-2xl font-bold text-emerald-600">
                            ${{ (parseFloat(form.basic_salary || 0) + parseFloat(form.bonus || 0) - parseFloat(form.deduction || 0)).toFixed(2) }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Base Salary</label>
                            <input v-model="form.basic_salary" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Bonus (+)</label>
                            <input v-model="form.bonus" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Deduction (-)</label>
                            <input v-model="form.deduction" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Payment Method</label>
                            <select v-model="form.payment_method" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Payment Date</label>
                            <input v-model="form.paid_at" type="date" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Notes</label>
                        <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Optional details..."></textarea>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Processing...' : 'Confirm Payment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
